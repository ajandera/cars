<?php
/**
 * Instacover API Access Handler
 *
 * This script handles API access for instacover integration.
 *
 * @package Instacover
 * @version 0.9
 */

/**
 * Check if HACORE is defined.
 *
 * This prevents direct access to the file and ensures it is included in the correct context.
 */

use Core\Instacover\Controller\InstacoverController;

require_once "_config_instacover.php";

if ( !defined('HACORE') ) {
    exit;
}

// require vendor autoload if exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    error_log('InstacoverController missing');
    exit;
}

$instacover = new InstacoverController(
    INSTACOVER_ZASTAVY_TABLE,
    INSTACOVER_IMAGE_FOLDER,
    INSTACOVER_CLIENT_ID,
    INSTACOVER_CLIENT_SECRET,
    INSTACOVER_BASEURI,
    CALLBACK_URL,
    INSTACOVER_SALT,
SETTING_TABLE);
$instacover->getSession();
