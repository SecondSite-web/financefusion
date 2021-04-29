<?php
/*
 *  Template Controller page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

$template = "open_ticket.twig";
$userGroups = $dashAuth->getGroups();
$users = $dashAuth->getAllUsers();

$values = array(
    'page' => array(
        'title'         => "Open a ticket",
        'description'   => "Open a New Ticket",
        'class'         => "open-ticket",
        'pic'           => ""
    ),
    'userGroups'        => $userGroups,
    'users'             => $users
);
echo $twig->render($template, $values);
