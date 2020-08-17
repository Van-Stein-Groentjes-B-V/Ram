<?php

header("Access-Control-Allow-Origin: *");

function curPageURL() {
    $pageURL = 'http';
    if ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") || $_SERVER['SERVER_PORT'] == 443) {
      $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

$url = curPageURL();
require_once dirname(dirname(__FILE__)).'/library/sg_autoload.php';
\SG\Ram\Router::start();