<?php
/**
 * Controller for Instacover API Access
 */

namespace Core\Instacover\Controller;

use Core\Instacover\Models\Poptavka;
use GuzzleHttp\Client;
use Core\Instacover\Helpers\Hash;
use Core\Instacover\Helpers\ImageUploader;

if (!defined('HACORE')) {
    exit;
}

class InstacoverController
{
    private string $table;
    private string $uploadDir;
    private Client $client;
    private array $clientId;
    private array $clientSecret;
    private string $baseUri;
    private Hash $hash;
    private string $settingTable;

    private string $callbackUrl;

    public function __construct(
        string $table,
        string $uploadDir,
        array $clientId,
        array $clientSecret,
        string $baseUri,
        string $callbackUrl,
        string $salt,
        string $settingTable)
    {
        $this->table = $table;
        // Ensure the upload directory ends with a slash
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 10,
        ]);
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->hash = new Hash($salt);
        $this->callbackUrl = $callbackUrl;
        $this->settingTable = $settingTable;
    }

    public function getSession()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(405, ['error' => 'Invalid request method.']);
            return;
        }

        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendResponse(400, ['error' => 'Invalid JSON format.']);
            return;
        }

        if (!isset($data['id'])) {
            $this->sendResponse(400, ['error' => 'Missing required POST parameter: id']);
            return;
        }

        $poptavka = new Poptavka($data['id'], $this->table);

        $accessToken = $this->getAccessToken($poptavka->getState());

        if ($accessToken === null) {
            $this->sendResponse(401, ['error' => 'Unauthorized. Invalid or missing token.']);
            return;
        }

        try {
            $url = $this->callbackUrl . '/object/instacover/object.instacover.callback?id=' . $this->hash->hashData($data['id']);
            $res = $this->client->post('/instacar/v2.0/session/create', [
                'json' => [
                    'callbackUrl' => $url,
                    'forcedFilesystemPhotoUpload' => true,
                    'documentsFilesystemPhotoUpload' => true,
                    "steps" => [
                        "Vin",
                        "Odometer",
                        "Windscreen",
                        "RearLeftSide",
                        "RearRightSide",
                        "FrontLeftSide",
                        "FrontRightSide",
                        "VehicleRegistrationCertificateFront",
                        "VehicleRegistrationCertificateBack"
                    ],
                    "optionalSteps" => [
                        "IdentityCardFront",
                        "IdentityCardBack",
                        "OtherDocument"
                    ],
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            if ($res->getStatusCode() !== 200 && $res->getStatusCode() !== 201) {
                $this->sendResponse(200, [
                    'status' => 'ERROR',
                    'msg' => 'Session not created'
                ]);
            }

            $payload = json_decode($res->getBody()->getContents(), true);

            $res = $poptavka->saveSesionId($payload['sessionId']);

            $response = [
                'status' => $res ? 'OK' : 'ERROR',
                'msg' => $res ? 'Session created' : 'session not saved',
                'url' => $payload['link'],
                'session' => $payload['sessionId'],
                'callback' => $url
            ];

            $this->sendResponse(200, $response);

        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    public function callback() 
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(405, ['error' => 'Invalid request method.']);
            return;
        }

        if (!isset($_GET['id']) && !isset($_POST['id'])) {
            $this->sendResponse(400, ['error' => 'Missing required GET parameter: id']);
            return;
        }

        $accessToken = $this->getAccessToken();

        if ($accessToken === null) {
            $this->sendResponse(401, ['error' => 'Unauthorized. Invalid or missing API key.']);
            return;
        }

        try {

            $sessionId = isset($_GET['id']) ? $_GET['id'] : $_POST['id']; 
            $decodedId = $this->hash->decodeHash($sessionId);
            if ($decodedId === null) {
                $this->sendResponse(400, ['error' => 'Invalid id parameter.']);
                return;
            }

            $id = (int) $decodedId;
            $row = new Poptavka($id, $this->table);
            $sessionId = $row->getPoptavkaSessionId();

            $res = $this->client->post('/instacar/v2.0/session/result', [
                'json' => [
                    'sessionId' => $sessionId
                ],
                'headers' => [
                    'Accept' => 'application/json', 
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            // Get response body and decode JSON
            $body = $res->getBody()->getContents();

            $responseData = json_decode($body, true);
            
            if (!empty($responseData['photos'])) {
                $uploader = new ImageUploader($responseData['photos'], $decodedId, $this->uploadDir, $this->client);
                $photosSaved = $uploader->upload();

                // mark as done 
                $row->saveSesionId("done_" . $sessionId);
                
                //INST-8 send notification
                $text = l("Poptavka c. %no% dokoncena. Uzivatel dokoncil foceni pres sluzbu Instacover.", ['%no%' => $id]) ;
                $subject = l("Poptavka Instacover dokonceno.");
                send_email($text, "obchod@cash4car.cz", $subject);
                $row->saveDoneSatus();

                $response = [
                    'status' => 'OK',
                    'msg' => 'Images saved',
                    'photos_saved' => $photosSaved
                ];
            } else {
                $response = [
                    'status' => 'OK',
                    'msg' => 'No images found',
                    'photos_saved' => null
                ];
            }

            $this->sendResponse(200, $response);

        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Sends a JSON response with the specified status code and data.
     *
     * @param int $statusCode
     * @param array $data
     * @return void
     */
    private function sendResponse(int $statusCode, array $data)
    {
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    private function getAccessToken(string $key): ?string
    {
        try {
            // Try to fetch token from DB
            $token = getConfig('instacover_token');

            $exp = getConfig('instacover_expiration');

            if ($token && $exp) {
                // if not expired, return it
                if (strtotime($exp) > time()) {
                    return $token;
                }
            }

            // Otherwise, request a new token
            $res = $this->client->post('/oauth/v1.0/token', [
                'form_params' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->clientId[$key],
                    'client_secret' => $this->clientSecret[$key],
                    'scope'         => 'categorization',
                ],
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            if ($res->getStatusCode() !== 200 && $res->getStatusCode() !== 201) {
                return null;
            }

            $payload = json_decode($res->getBody()->getContents(), true);

            if (empty($payload['access_token'])) {
                return null;
            }

            $accessToken = $payload['access_token'];
            $expiresIn   = isset($payload['expires_in']) ? (int) $payload['expires_in'] : 3600;

            // Calculate expiration timestamp (5 minutes safety margin)
            $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn - 300);

            // Save into DB (update if exists, insert otherwise)
            // First check if row exists
            if ($token && $exp) {
                coreDBEditSingle($this->settingTable, "value", $accessToken, "alias = 'instacover_token'");
                coreDBEditSingle($this->settingTable, "value", $expiresAt, "alias = 'instacover_expiration'");
            } else {
                coreDBInsert($this->settingTable, [
                    "alias"    => 'instacover_token',
                    "access_token" => $accessToken,
                    "kategorie" => "_CORE",
                    "subkategorie" => "API"
                ]);

                coreDBInsert($this->settingTable, [
                    "alias"    => 'instacover_expiration',
                    "value" => $expiresAt,
                    "kategorie" => "_CORE",
                    "subkategorie" => "API"
                ]);
            }

            return $accessToken;

        } catch (\Exception $e) {
            // Log or handle
            return null;
        }
    }
}