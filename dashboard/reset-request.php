<?php
/*
 *  Template Controller page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;

$pagepic = ""; // The featured image of the page default is a 1200 x 630 .png

$values = array(
    'page' => array(
        'title'         => "Password Reset Request",
        'description'   => "Request a password reset",
        'class'         => "reset-request",
        'pic'           => ""
    )
);
echo $twig->render($template, $values);
