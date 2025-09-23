<?php

global $C;

define( 'INSTACOVER_ZASTAVY_TABLE', $C->db_prefix . 'poptavky' );
define( 'INSTACOVER_IMAGE_FOLDER', $C->ROOT_PATH . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'instacover' . DIRECTORY_SEPARATOR );
define('INSTACOVER_BASEURI', "https://api.instacover.ai");
define( 'INSTACOVER_FILES_TABLE', $C->db_prefix . 'prirazene_obrazky');
define( 'INSTACOVER_DATABASE_ALIAS', 'instacover_poptavky');
define('CALLBACK_URL', getConfig('instacover_callback'));
define('SETTING_TABLE', $C->db_prefix . 'settings');
header('Content-type: application/json');