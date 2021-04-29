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
            'user_id' => 'trim|sanitize_string|lower_case',
            'dest_group'  => 'trim|sanitize_string|lower_case',
            'subject'     => 'trim|sanitize_string|lower_case',
            'message' => 'trim|sanitize_string|lower_case',
            'priority'     => 'trim|sanitize_email|lower_case',
            'status'   => 'trim|sanitize_string|lower_case'
        );
        // Gump rules for the input fields
        $rules = array(
            'user_id' => 'required|alpha_numeric|max_len,25|min_len,1',
            'dest_group'  => 'required|alpha_numeric|max_len,25|min_len,1',
            'subject'     => 'required|alpha_numeric',
            'message' => 'required|alpha_numeric',
            'priority'     => 'required|alpha_numeric',
            'status'   => 'required|alpha_numeric',
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
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'open-ticket-form');
        if ($nonceTest === false) {
            throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }

        if (empty($_POST['subject']) || empty($_POST['message'])) {
            throw new Exception('The form has not been filled in completely');
        }

        $saveTicket = $crm->saveTicket($_POST);
        if ($saveTicket === false) {
            throw new Exception('Error Saving Ticket to the database');
        }
        $userDetails = $auth->getUser($_POST['user_id'], false);
        $destGroup = $dashAuth->getGroupById($_POST['dest_group']);
        $fields = array(
            'title' => 'A new ticket has been opened',
            'subject' => $_POST['subject'],
            'message' => $_POST['message'],
            'priority' => $_POST['priority'],
            'status' => $_POST['status'],
            'name' => $userDetails['firstname'] . " " . $userDetails['lastname'],
            'email' => $userDetails['email'],
            'group' => $destGroup['user_group']
            );
        $groupEmails = $dashAuth->getEmailsByGroupId($_POST['dest_group']);

        $mailTicket = $crm->sendMail($fields, $groupEmails);
        if ($mailTicket === false) {
            throw new Exception('Error Saving Ticket to the database');
        }

        $okMessage = 'Your ticket has been opened';
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
