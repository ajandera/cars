<?php

use Core\Ecomail\Controller\EcomailController;

require_once "_config_ecomail.php";

if ( !defined('HACORE') ) {
    exit;
}

// require vendor autoload if exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    error_log('EcomailController missing');
    exit;
}

$instacover = new EcomailController(
    POPTAVKY_TABLE,
    ECOMAIL_API_KEY,
    SETTING_TABLE
);
$instacover->export();