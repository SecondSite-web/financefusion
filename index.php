<?php
require_once __DIR__ .'/dash-loader.php';
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);

if ($auth->isLogged()) {
    header("Location: ".SITE_URL."/dashboard/");
}
$template = "user_login.twig";

$values = array(
    'page' => array(
        'title' => "Login to Finance Fusion Trading Game",
        'description' => "Please login with your given credentials",
        'class' => "login",
        'pic' => IMG_URL.'/site-img.png'
    )
);

echo $twig->render($template, $values);
