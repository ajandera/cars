<?php
/**
 * FilePackager Class
 */
namespace Core\Instavocer\Helpers;

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

    public function __construct( array $images, int $recordId , string $dir)
    {
        $this->images = $images;
    }

    public function upload()
    {
        // create folder if not existst
        $uploadDir = $this->dir . "/" . $this->recordId;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // process uploaded files
        $uploadedFiles = [];
        foreach ($this->images['tmp_name'] as $key => $tmpName) {
            if ($this->images['error'][$key] === UPLOAD_ERR_OK) {
                $filename = basename($this->images['name'][$key]);
                $targetFile = $uploadDir . "/" . $filename;

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $uploadedFiles[] = $filename;
                }
            }
        }
    }

}