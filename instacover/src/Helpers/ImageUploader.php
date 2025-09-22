<?php
/**
 * FilePackager Class
 */
namespace Core\Instacover\Helpers;

use GuzzleHttp\Client;

if (!defined('HACORE')) {
    exit;
}

/**
 * Class FilePackager
 *
 * This class is responsible for packaging files from a source directory to a target directory.
 */
class ImageUploader
{
    /**
     * @var array $images List of files to be packaged
     */
    private array $images = [];
    private int $recordId;
    private string $dir;
    private Client $client;

    public function __construct( array $images, int $recordId , string $dir, Client $client)
    {
        $this->images = $images;
        $this->recordId = $recordId;
        $this->dir = rtrim($dir, '/') . '/';
        $this->client = $client;
    }

    public function upload(): array
    {
        // Save images from photos array
        $photosSaved = [];
        if (isset($this->images) && is_array($this->images)) {
            $uploadDir = $this->dir . $this->recordId . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            foreach ($this->images as $photo) {
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
                            coreDBInsert(
                                INSTACOVER_FILES_TABLE,
                                ['id_souvisi', 'prirazeni_alias', 'obrazek', 'stav', 'poradi'],
                                ['id_souvisi' => $this->recordId,
                                'prirazeni_alias' => INSTACOVER_DATABASE_ALIAS,
                                'obrazek' => $filePath,
                                'poradi' => (count($photosSaved))]);
                        }
                    } catch (\Exception $e) {
                        // Could not download image, skip
                    }
                }
            }
        }
        return $photosSaved;
    }

}