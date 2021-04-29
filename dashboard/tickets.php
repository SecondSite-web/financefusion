<?php
/*
 *  Template Controller page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

use Dash\Crm;

$crm = new Crm($pdo);

$template = "tickets-table.twig";

$userGroups = $dashAuth->getGroups();
$tickets = $crm->getAllTickets();
$ticketMeta = $crm->getAllMeta();
$users = $dashAuth->getAllUsers();

$values = array(
    'page' => array(
        'title'         => "User List",
        'description'   => "Table of all Users",
        'class'         => "cf-tables",
        'pic'           => ""
    ),
    'tickets'           => $tickets,
    'userGroups'        => $userGroups,
    'ticketMeta'        => $ticketMeta,
    'users'             => $users
);
echo $twig->render($template, $values);
