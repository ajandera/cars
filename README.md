# update sql

ALTER TABLE `core_poptavky` ADD `stav_photoscompleted` TINYINT(1) NOT NULL DEFAULT '0' AFTER `instacover_session_id`;