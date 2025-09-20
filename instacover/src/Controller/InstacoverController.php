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
    private string $clientId;
    private string $clientSecret;
    private string $baseUri;
    private Hash $hash;

    private string $callbackUrl;

    public function __construct(
        string $table,
        string $uploadDir,
        string $clientId,
        string $clientSecret,
        string $baseUri,
        string $callbackUrl,
        string $salt)
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

    public function callback() 
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendResponse(405, ['error' => 'Invalid request method.']);
            return;
        }

        if (!isset($_GET['id'])) {
            $this->sendResponse(400, ['error' => 'Missing required GET parameter: id']);
            return;
        }

        $accessToken = $this->getAccessToken();

        if ($accessToken === null) {
            $this->sendResponse(401, ['error' => 'Unauthorized. Invalid or missing API key.']);
            return;
        }

        try {

            $decodedId = $this->hash->decodeHash($_GET['id']);
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
            
            // test mock
            //$body = file_get_contents(__DIR__ ."/mock.json");

            $responseData = json_decode($body, true);
            
            if (!empty($responseData['photos'])) {
                $uploader = new ImageUploader($responseData['photos'], $decodedId, $this->uploadDir, $this->client);
                $photosSaved = $uploader->upload();

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

    private function getAccessToken(): ?string
    {
        try {
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
            return $payload['access_token'] ?? null;

        } catch (\Exception $e) {
            // Log the exception or handle it as needed\
            return null;
        }
    }
}