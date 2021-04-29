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

// Page Template details
$template = "closed-tickets-table.twig";
$pagetitle = "User List";
$description = "Table of all Users";
$pagepic = ""; // The featured image of the page default is a 1200 x 630 .png
$class = "cf-tables";

$userGroups = $dashAuth->getGroups();
$tickets = $crm->getAllTickets();
$ticketMeta = $crm->getAllMeta();
$users = $dashAuth->getAllUsers();

$values = array(
    'page' => array(
        'title'         => $pagetitle,
        'description'   => $description,
        'class'         => $class,
        'pic'           => $pagepic
    ),
    'tickets'=> $tickets,
    'userGroups' => $userGroups,
    'ticketMeta' => $ticketMeta,
    'users'     => $users
);
echo $twig->render($template, $values);
