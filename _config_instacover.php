<?php

global $C;

$clientId = [
    'CZ' => getConfig('instacover_client_id'),
    'DE' => getConfig('instacover_client_id_de'),
    'PL' => getConfig('instacover_client_id_pl'),
    'ES' => getConfig('instacover_client_id_es')
];

$clientSecret = [
    'CZ' => getConfig('instacover_client_secret'),
    'DE' => getConfig('instacover_client_secret_de'),
    'PL' => getConfig('instacover_client_secret_pl'),
    'ES' => getConfig('instacover_client_secret_es')
];

define( 'INSTACOVER_ZASTAVY_TABLE', $C->db_prefix . 'poptavky' );
define( 'INSTACOVER_IMAGE_FOLDER', $C->ROOT_PATH . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'instacover' . DIRECTORY_SEPARATOR );
define('INSTACOVER_BASEURI', "https://api.instacover.ai");
define( 'INSTACOVER_FILES_TABLE', $C->db_prefix . 'prirazene_obrazky');
define( 'INSTACOVER_DATABASE_ALIAS', 'instacover_poptavky');
define('CALLBACK_URL', getConfig('instacover_callback'));
define('SETTING_TABLE', $C->db_prefix . 'settings');
define('INSTACOVER_CLIENT_ID', $clientId);
define('INSTACOVER_CLIENT_SECRET', $clientSecret);
define('INSTACOVER_SALT', getConfig('instacover_salt'));
header('Content-type: application/json');