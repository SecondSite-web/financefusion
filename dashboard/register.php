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
        'title'         => "User Registration",
        'description'   => "User Registration Page",
        'class'         => "register",
        'pic'           => ""
    )
);
echo $twig->render($template, $values);
