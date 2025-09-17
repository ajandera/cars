<?php
/**
 * Controller for Instacover API Access
 */

namespace Core\Instacover\Controller;

use Core\Instacover\Helpers\DatabaseHandler;
use Core\Instacover\Models\Zastava;
use GuzzleHttp\Client;
use DateTime;
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
    private string $username;
    private string $password;
    private string $baseUri;
    private Hash $hash;

    public function __construct(
        string $table,
        string $uploadDir,
        string $username,
        string $password,
        string $baseUri)
    {
        $this->table = $table;
        // Ensure the upload directory ends with a slash
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 10,
        ]);
        $this->username = $username;
        $this->password = $password;
        $this->hash = new Hash();
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
            $this->sendResponse(401, ['error' => 'Unauthorized. Invalid or missing API key.']);
            return;
        }

        try {
            $res = $this->client->post('/instacar/v2.0/session/create', [
                'json' => [
                    'callbackUrl' => 'core.local/api/instacover/callback?id=' . $this->hash->hashData($data['id'])
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Bearer' => $accessToken
                ],
            ]);

            $zastava = new Zastava($data['id'], $this->table);
            $res = $zastava->saveSesionId($res['sessionId']);

            $response = [
                'status' => 'OK',
                'msg' => 'Session created'
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
            $row = new Zastava($decodedId, $this->table);
            $sessionId = $row->getZastavaSessionId();

            $res = $this->client->post('/instacar/v2.0/session/result', [
                'json' => [
                    'sessionId' => $sessionId
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Bearer' => $accessToken
                ],
            ]);

            // Get response body and decode JSON
            $body = $res->getBody()->getContents();
            $responseData = json_decode($body, true);
            $uploader = new ImageUploader($responseData['photos'], $decodedId, $this->uploadDir, $this->client);
            $photosSaved = $uploader->upload();

            $response = [
                'status' => 'OK',
                'msg' => 'Session result processed',
                'photos_saved' => $photosSaved
            ];

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
            $res = $this->client->post('/auth/login', [
                'json' => [
                    'grant_type' => 'all',
                    'username' => $this->username,
                    'password' => $this->password,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            if ($res->getStatusCode() !== 200 && $res->getStatusCode() !== 201) {
                echo "Login failed: " . $res->getBody();
                exit(1);
            }

            $payload = json_decode($res->getBody()->getContents(), true);
            $token = $payload['token'] ?? $payload['access_token'] ?? null;

            if (!$token) {
                echo "No token in response\n";
                exit(1);
            }

            return $token;

        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            return null;
        }
    }
}