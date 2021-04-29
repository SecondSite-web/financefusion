<?php
require_once __DIR__ .'/dash-loader.php';
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

$template = "contact_page.twig";

$values = array(
    'page' => array(
        'title' => "Contact Us",
        'description' => "Please complete the contact form and we will get back to you shortly",
        'class' => "contact",
        'pic' => IMG_URL.'/site-img.png'
    )
);

echo $twig->render($template, $values);
