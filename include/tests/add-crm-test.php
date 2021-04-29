<?php
/**
 * File:          add-post-test.php
 * File Created:  2021/04/11 16:47
 * Modified By:   Gregory Schoeman <gregory@secondsite.xyz>
 * PHP version 8.0
 * -----
 *
 * @category  WebApp
 * @package   NPM
 * @author    Gregory Schoeman <gregory@secondsite.xyz>
 * @copyright 2019-2021 SecondSite
 * @license   https://opensource.org/licenses/MIT  MIT
 * @version   GIT: <1.0.0>
 * @link      https://github.com/SecondSite-web/dash.git
 * @project   dash
 */
require_once __DIR__ . "/../../dash-loader.php";
use Dash\Crm;

$crm = new Crm($pdo);
// foreach (range(0, 100000) as $i) {
$post = array(
    'user_id' => 1,
    'dest_group' => 1,
    'subject' => 'Problem finding something',
    'message' => 'Im having a problem locating the following files',
    'priority' => 'high',
    'status' => 'submitted'
);
// $result = $crm->saveTicket($post);

$meta = array(
    array(
        'ticket_id' => 2,
        'user_id' => 1,
        'message' => '3rd message',
    )
);

$ticketId = 2;
$saveMeta = $crm->saveTicketMeta($meta, $ticketId);
if($saveMeta === true){
    echo true;
}

$ticket = $crm->getTicketById($ticketId);
print_r($ticket);
$ticketMeta = $crm->getTicketMetaById($ticketId);
print_r($ticketMeta);

$ticketStatus = $crm->setTicketStatus('resolved', 1);

