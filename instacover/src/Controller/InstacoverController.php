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

/**
 * InstacoverController
 *
 * This controller handles API interactions with the Instacover service, including session creation,
 * callback processing, and access token management. It integrates with Guzzle for HTTP requests,
 * manages session IDs and image uploads, and provides JSON responses for API endpoints.
 *
 * Main responsibilities:
 * - Create Instacover sessions and save session IDs.
 * - Handle callbacks from Instacover, fetch session results, and save uploaded images.
 * - Manage OAuth access tokens, including caching and refreshing.
 * - Provide utility methods for sending JSON responses.
 *
 * Dependencies:
 * - Core\Instacover\Models\Poptavka: Model for handling session IDs and related data.
 * - GuzzleHttp\Client: HTTP client for API requests.
 * - Core\Instacover\Helpers\Hash: Helper for hashing and decoding IDs.
 * - Core\Instacover\Helpers\ImageUploader: Helper for saving images from Instacover.
 *
 * Usage:
 * Instantiate with required configuration and call public methods for API endpoints.
 */
class InstacoverController
{
    private string $table;
    private string $uploadDir;
    private Client $client;
    private string $clientId;
    private string $clientSecret;
    private string $baseUri;
    private Hash $hash;
    private string $settingTable;

    private string $callbackUrl;


    /**
     * @param string $table
     * @param string $uploadDir
     * @param string $clientId
     * @param string $clientSecret
     * @param string $baseUri
     * @param string $callbackUrl
     * @param string $salt
     * @param string $settingTable
     */
    public function __construct(
        string $table,
        string $uploadDir,
        string $clientId,
        string $clientSecret,
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

    /**
     * Summary of getSession
     * @return void
     */
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

        $accessToken = $this->getAccessToken();

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
                    'documentsFilesystemPhotoUpload' => true
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

            $poptavka = new Poptavka($data['id'], $this->table);

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

    /**
     * Summary of callback
     * @return void
     */
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

    /**
     * Summary of getAccessToken
     * @return string|null
     */
    private function getAccessToken(): ?string
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
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
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