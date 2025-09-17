<?php
/**
 * Controller for Instacover API Access
 */

namespace Core\Instacover\Controller;

use Core\Instacover\Helpers\DatabaseHandler;
use Core\Instacover\Models\Zastava;
use GuzzleHttp\Client;
use DateTime;

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
    private string $salt = 'dklshf3245ased3';

    public function __construct(string $table, string $uploadDir, string $username, string $password, string $baseUri)
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

        $this->validateRequestData($data);

        $accessToken = $this->getAccessToken();

        if ($accessToken === null) {
            $this->sendResponse(401, ['error' => 'Unauthorized. Invalid or missing API key.']);
            return;
        }

        try {
            $res = $this->client->post('/instacar/v2.0/session/create', [
                'json' => [
                    'callbackUrl' => 'core.local/api/instacover/callback?id=' . $this->hashData($data['id'])
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

            $decodedId = $this->decodeHash($_GET['id']);
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

            // Save images from photos array
            $photosSaved = [];
            if (isset($responseData['photos']) && is_array($responseData['photos'])) {
                $uploadDir = $this->uploadDir . $decodedId . '/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                foreach ($responseData['photos'] as $photo) {
                    if (isset($photo['link'])) {
                        $photoUrl = $photo['link'];
                        $photoId = $photo['photoId'] ?? uniqid('photo_');
                        $photoType = $photo['type'] ?? 'unknown';
                        $ext = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                        $filename = $photoType . '_' . $photoId . ($ext ? '.' . $ext : '.jpg');
                        $filePath = $uploadDir . $filename;

                        // Download image and save to disk
                        try {
                            $imgRes = $this->client->get($photoUrl, ['sink' => $filePath]);
                            if ($imgRes->getStatusCode() === 200 && file_exists($filePath)) {
                                $photosSaved[] = $filename;
                            }
                        } catch (\Exception $e) {
                            // Could not download image, skip
                        }
                    }
                }
            }

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
     * Decodes the base64 string and saves it as a PDF file.
     *
     * @param array $data The validated request data.
     * @return array An array indicating the result of the operation.
     */
    private function saveInfractionPdf(array $data, int $carId): array
    {
        // Extract variables for clarity.
        $vehicle = $data['vehicle'] ?? 'unknown_vehicle';
        $reference = $data['reference'] ?? 'unknown_ref';
        $infraction_date = $data['infraction_date'] ?? 'unknown_date';
        if ($infraction_date !== 'unknown_date') {
            $infraction_date = DateTime::createFromFormat('Y-m-d\TH:i', $infraction_date);
            if ($infraction_date) {
                $infraction_date = $infraction_date->format('Ymd_Hi');
            }
        }

        $notification_doc_base64 = $data['notification_doc'] ?? null;

        if (empty($notification_doc_base64)) {
            return ['success' => false, 'message' => 'No PDF data provided in notification_doc.'];
        }

        // Decode the base64 string to binary PDF data.
        $pdfData = base64_decode($notification_doc_base64);
        if ($pdfData === false) {
            return ['success' => false, 'message' => 'Failed to decode base64 string. The data may be corrupt.'];
        }

        // Ensure the upload directory exists. If not, try to create it.
        $uploadDir = $this->uploadDir . $carId . '/';

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => "Upload directory '{$uploadDir}' does not exist and could not be created."];
            }
        }

        // Create a unique and descriptive filename.
        $filename = "infraction_{$reference}_{$vehicle}_{$infraction_date}.pdf";
        $filePath = $uploadDir . $filename;

        $dbHandler = new DatabaseHandler($filePath, $carId);
        $dbHandler->save();

        // Write the binary data to the file.
        if (file_put_contents($filePath, $pdfData) !== false) {
            return ['success' => true, 'message' => 'PDF saved successfully.', 'filename' => $filename];
        } else {
            return ['success' => false, 'message' => 'Failed to write PDF to disk. Please check directory permissions.'];
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
     * Validates the required fields in the request data.
     *
     * @param array $data The request data to validate.
     * @return void
     */
    private function validateRequestData(array $data)
    {
        $requiredFields = [
            'id' => 'numeric'
        ];

        foreach ($requiredFields as $field => $type) {
            if (!isset($data[$field])) {
                $this->sendResponse(400, ['error' => "Missing required field: $field"]);
                return;
            }

            if ($type === 'date') {
                $date = DateTime::createFromFormat('Y-m-d\TH:i', $data[$field]);
                if (!$date || $date->format('Y-m-d\TH:i') !== $data[$field]) {
                    $this->sendResponse(400, ['error' => "Invalid date format for field: $field. Expected format YYYY-MM-DDTHH:MM."]);
                    return;
                }
            }

            if ($type === 'numeric' && !is_numeric($data[$field])) {
                $this->sendResponse(400, ['error' => "Field must be numeric: $field"]);
                return;
            }
        }
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

    /**
     * Hashes the given data using a secure algorithm.
     *
     * @param mixed $data
     * @return string
     */
    private function hashData($data): string
    {
        return base64_encode($data . '|' . $this->salt);
    }

    /**
     * Decodes the hash and returns the original id if valid.
     *
     * @param string $hash
     * @return mixed|null
     */
    private function decodeHash(string $hash)
    {
        $decoded = base64_decode($hash, true);
        if ($decoded === false) {
            return null;
        }
        $parts = explode('|', $decoded);
        if (count($parts) !== 2 || $parts[1] !== $this->salt) {
            return null;
        }
        return $parts[0];
    }
}