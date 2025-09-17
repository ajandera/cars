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

global $C;

define( 'INSTACOVER_ZASTAVY_TABLE', $C->db_prefix . 'zastavy' );
define( 'INSTACOVER_IMAGE_FOLDER', 'files/instacover/' );
define('INSTACOVER_USERNAME', "xxx");
define('INSTACOVER_PASSWORD', "zzz");
define('INSTACOVER_BASEURI', "https://api.instacover.ai");

header('Content-type: application/json');

$instacover = new InstacoverController(
    INSTACOVER_ZASTAVY_TABLE,
    INSTACOVER_IMAGE_FOLDER,
    INSTACOVER_USERNAME,
    INSTACOVER_PASSWORD,
    INSTACOVER_BASEURI);
$instacover->getSession();
