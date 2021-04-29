<?php

require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

use Dash\Crm;
use Dash\DashAuth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);

$dashAuth = new DashAuth($pdo);
$crm= new Crm($pdo);
$log = new Logger('Open Ticket Form');
$log->pushHandler(new StreamHandler(ROOT_PATH.'/logs/tickets.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'ticket_id' => 'trim|sanitize_string',
            'user_id' => 'trim|sanitize_string',
            'message' => 'trim|sanitize_string',
        );
        // Gump rules for the input fields
        $rules = array(
            'ticket_id'  => 'required|alpha_numeric|max_len,25|min_len,1',
            'user_id' => 'required|alpha_numeric|max_len,25|min_len,1',
            'message' => 'required|alpha_numeric',
            'nonce'     => 'required'
        );
        $validator = new GUMP();
        $whitelist = array_keys($rules);
        $_POST = $validator->sanitize($_POST, $whitelist);
        $_POST = $validator->filter($_POST, $filters);
        $validated = $validator->validate($_POST, $rules);
        if ($validated === false) {
            throw new \Exception($validator->get_readable_errors(true));
        }
        if (empty($_POST['nonce'])) {
            throw new Exception('missing nonce');
        }
        $nonceUtil = $dashNonce->nonceInit();
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'reply-ticket-form');
        if ($nonceTest === false) {
            throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }

        $result = $crm->saveMeta($_POST);
        if ($result === false) {
            throw new Exception('error saving to database');
        }
        $userDetails = $auth->getUser($_POST['user_id'], false);

        $ticket = $crm->getTicketById($_POST['ticket_id']);

        $destGroup = $dashAuth->getGroupById($ticket['dest_group']);

        $fields = array(
            'ticket_id' => $_POST['ticket_id'],
            'title' => 'Ticket Reply',
            'subject' => $ticket['subject'],
            'message' => $_POST['message'],
            'priority' => $ticket['priority'],
            'status' => $ticket['status'],
            'name' => $userDetails['firstname'] . " " . $userDetails['lastname'],
            'email' => $userDetails['email'],
            'group' => $destGroup['user_group']
        );

        $groupEmails = $dashAuth->getEmailsByGroupId($ticket['dest_group']);
        $mailTicket = $crm->sendMail($fields, $groupEmails);
        $okMessage = 'Your reply has been sent!';

        $responseArray = array('type' => 'success', 'message' => $okMessage);
        $log->info('Successful', $_POST);
    } catch (\Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
        $log->error('Failed', array($_POST, $e->getMessage()));
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $encoded = json_encode($responseArray);

        header('Content-Type: application/json');

        echo $encoded;
    } else {
        echo $responseArray['message'];
    }
} else {
    header("Location: /");
}
