<?php
/*
 *  Twig Admin Center Index Page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

$template = "user_user_groups.twig";
$userGroups = $dashAuth->getGroups();
$users = $dashAuth->getAllUsers();
$userRoles = $dashAuth->getRoles();
$values = array(
    'page' => array(
        'title'         => 'Add a new user',
        'description'   => 'Admin add new user form',
        'class'         => 'addUser',
        'pic'           => IMG_URL.'/site-img.png'
    ),
    'userGroups'        => $userGroups,
    'userRoles'         => $userRoles,
    'users'             => $users
);
echo $twig->render($template, $values);
