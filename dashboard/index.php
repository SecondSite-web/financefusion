<?php
/*
 *  Twig Admin Center Index Page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

use Dash\Crm;
use Dash\Cf;

$crm = new Crm($pdo);
$cf = new Cf($pdo);

$template = "sb_dash.twig";

$openTickets = $crm->openTicketCount('closed');
$groups = $dashAuth->getGroups();
$counts = $crm->getAllCounts($groups);
$contacts = $cf->openCount();
$userCount = $dashAuth->getUserCount();
$groupUsers = $dashAuth->getAllCounts($groups);

$values = array(
    'page' => array(
        'title'         => "Dashboard",
        'description'   => "Welcome to your dashboard",
        'class'         => "dashboard",
        'pic'           => ""
    ),
    'openTicket'        => $openTickets,
    'groups'            => $groups,
    'counts'            => $counts,
    'contacts'          => $contacts,
'userCount'             => $userCount,
    'groupUsers'        => $groupUsers
);
echo $twig->render($template, $values);
