<?php

if(!defined("HACORE")) die("Hack detected!");

function html_page_head($needle,$header_array=false,$addOn = "")
{
	global $C,$core,$C_header_array, $C_dir,$C_text_adresa,$_display,$_SESSION;

	if(!$header_array)
	{	$header_array = $C_header_array;
	}else{
		foreach ($C_header_array as $key => $z)
		{
			if(!isset($header_array[$key])) $header_array[$key] = $z;
		}
	}


header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: private");


echo'<!DOCTYPE html>
<html lang="'.LANGUAGE.'">
<head>
<title>'.$core->getTitle().'</title>
<base href="'.$C_text_adresa.'" />
<meta name="csrf-token" content="'.$_SESSION['csrf_token'].'">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="'.$header_array["description"].'" />
<meta name="keywords" content="'.$header_array["keywords"].'" />
<meta name="author" content="'.$header_array["author"].'" />
<meta name="robots" content="noindex, nofollow" />
<meta name="googlebot" content="noindex, nofollow" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<meta property="fb:admins" content="1523471665"/>
<meta property="og:title" content="'.$header_array["title"].' | '.$header_array["nazev"].'"/>
<meta property="og:site_name" content="'.$header_array["nazev"].'"/>
<meta property="og:type" content="article"/>';

if (isset($header_array["url"]) && $header_array["url"])	echo '<meta property="og:url" content="'.$header_array["url"].'"/><link rel="canonical" href="'.$header_array["url"].'" />';
if (isset($header_array["image"]) && $header_array["image"])	echo '<meta property="og:image" content="'.$C_text_adresa.$header_array["image"].'"/><link rel="image_src" href="'.$C_text_adresa.$header_array["image"].'" />';
elseif (file_exists("images/layout/meta_image.jpg"))	echo '<meta property="og:image" content="'.$C_text_adresa.'images/layout/meta_image.jpg"/><link rel="image_src" href="'.$C_text_adresa.'images/layout/meta_image.jpg" />';


echo '

<meta http-equiv="Content-language" content="cs" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />

<link href="'.$C_dir.'css/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->


<link rel="stylesheet" type="text/css" href="'.$C_dir.'css/font-awesome/css/font-awesome.min.css?v=4.7.0" />
<link rel="stylesheet" type="text/css" href="'.$C_dir.'css/styles.css?20230921" />
<link rel="stylesheet" type="text/css" href="'.$C_dir.'_js/smoothness/jquery-ui-1.9.2.custom.min.css" />
<link href="https://vjs.zencdn.net/8.23.3/video-js.css" rel="stylesheet" />

<link rel="stylesheet" type="text/css" href="'.$C_dir.'_js/colorpicker/css/colorpicker.css" />
<link rel="stylesheet" type="text/css" href="'.$C_dir.'_js/lightbox/css/lightbox.css" media="screen" />

<script type="text/javascript" src="'.$C_dir.'_js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="'.$C_dir.'_js/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="'.$C_dir.'_js/jquery.qtip-1.0.min.js"></script>
<script type="text/javascript" src="'.$C_dir.'_js/lightbox/js/lightbox.min.js"></script>
<script type="text/javascript" src="'.$C_dir.'_js/colorpicker.js"></script>
<script type="text/javascript" src="'.$C_dir.'_js/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://vjs.zencdn.net/8.23.3/video.min.js"></script>
';

if (prava(1))
{
	echo '<link rel="stylesheet" type="text/css" href="'.$C_dir.'_js/fileupload/css/jquery.fileupload-ui.css?2">
<script type="text/javascript" src="'.$C_dir.'_js/fileupload/js/jquery.fileUploadAll.js?3"></script>

<link rel="stylesheet" href="'.$C_dir.'_js/multiselect/bootstrap-select.min.css" type="text/css"/>
';


}

if (prava(2))
{

echo '<script src="../_js/ckeditor4/ckeditor.js" type="text/javascript"></script>
<script src="../_js/ckeditor4/adapters/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function()
{
	$(".wysiwyg").ckeditor({contentsCss: "'.$C_dir.'css/styles.css"});
});
</script>';

}

if($_display=='login')
{
echo '<script src="https://www.google.com/recaptcha/api.js?render=6LcuYWgbAAAAAPTALS1X5tZ7VhsHhIKugV5nPzzf"></script>';

}
echo '
<script type="text/javascript" src="'.$C_dir.'js/scripts.js?language='.LANGUAGE.'&data202456874=0818&modified='.filemtime($C_dir.'js/scripts.js').'"></script>
<script type="text/javascript" src="'.$C_dir.'_js/scripts_faktury.js?data202300817=0817"></script>
<link rel="shortcut icon" href="'.$C_dir.'favicon.ico" />';
echo $addOn;
echo '</head><body class="p-'.clear_str($_display).'">';



}
