<?php
/*
 *  Twig Admin Center Index Page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

$template = "user_add_user.twig";
$userGroups = $dashAuth->getGroups();
$userRoles = $dashAuth->getRoles();
$values = array(
    'page' => array(
        'title'         => 'Add a new user',
        'description'   => 'Admin add new user form',
        'class'         => 'addUser',
        'pic'           => IMG_URL.'/site-img.png'
    ),
    'userGroups'        => $userGroups,
    'userRoles'         => $userRoles
);
echo $twig->render($template, $values);
