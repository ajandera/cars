<?php

global $C;

define( 'ECOMAIL_API_KEY', '' );
define( 'POPTAVKY_TABLE', $C->db_prefix . 'poptavky');
define('SETTING_TABLE', $C->db_prefix . 'settings');
header('Content-type: application/json');