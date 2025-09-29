<?php
/**
 * Controller for Ecomail API Access
 */

namespace Core\Ecomail\Controller;

use Core\Ecomail\Helper\Ecomail;

if (!defined('HACORE')) {
    exit;
}

class EcomailController
{
    private string $table;
    private string $apikey;
    private string $settingTable;

    public function __construct(
        string $table,
        string $apikey,
        string $settingTable)
    {
        $this->table = $table;
        $this->settingTable = $settingTable;
        $this->apikey = $apikey;
    }

    public function export()
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

        if (!isset($data['id']) && !isset($data['listId']) && !isset($data['dateFrom'])) {
            $this->sendResponse(400, ['error' => 'Missing required POST parameter: id or listId']);
            return;
        }

        // get poptavky ny time range an marketing = 1 a neni duplicitni
        $sql = coreDBSel("SELECT * FROM ".$this->table ."WHERE date_add > ? AND marketing = 1", $data['dateFrom']);
        $subscribers = [];
        foreach($sql->fetchRows() as $row) {
            $subscribers[] = [
                'email' => 'uzivatel1@example.com',
                'name' => 'Jan',
                'surname' => 'Novák'
            ];
        }

        $ecomail = new Ecomail($this->apikey);

        try {
            $response = $ecomail->subscribeBulk($data['listId'], $subscribers);
            $this->sendResponse(200, ['message' => 'success']);
        } catch (\Exception $e) {
            $this->sendResponse(500, ['message' => "Chyba při bulk subscribe: " . $e->getMessage()]);
        }
    }

    public function getLists() {
        $ecomail = new Ecomail($this->apikey);

        try {
            $this->sendResponse(200, ['message' => 'success', 'lists' => $ecomail->getLists()]);
        } catch (\Exception $e) {
            $this->sendResponse(500, ['message' => 'error', 'error' => $e->getMessage()]);
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
}