<?php

use Core\Ecomainl\Controller\EcomailController;

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
$ecomail = new EcomailController(
    POPTAVKY_TABLE,
    ECOMAIL_API_KEY,
    SETTING_TABLE
);
$ecomail->unsubscribe();