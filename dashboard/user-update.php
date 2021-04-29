<?php
/*
 *  Twig User Profile Page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

$template = "user_update.twig";

$values = array(
    'page' => array(
        'title'         => "User Profile Page",
        'description'   => "User profile details page",
        'class'         => "profile",
        'pic'           => ""
    )
);
echo $twig->render($template, $values);
