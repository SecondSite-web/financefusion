<?php
/*
 *  Twig Admin Center Index Page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';


$template = "user_account.twig";

$values = array(
    'page' => array(
        'title'         => 'My Account',
        'description'   => 'Update my user profile',
        'class'         => 'update',
        'pic'           => IMG_URL.'/site-img.png'
    ),
);
echo $twig->render($template, $values);
