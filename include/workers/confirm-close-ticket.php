<?php

require_once __DIR__ . "/../../dash-loader.php";
require_once FUNCTIONS_URL.'/lock.php';

use Dash\Crm;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$crm= new Crm($pdo);
$log = new Logger('Open Ticket Form');
$log->pushHandler(new StreamHandler(ROOT_PATH.'/logs/tickets.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (empty($_POST['nonce'])) {
            throw new Exception('missing nonce');
        }
        $nonceUtil = $dashNonce->nonceInit();
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'confirm-close-ticket-form');
        if ($nonceTest === false) {
            throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }

        $filters = array(
            'ticket_id' => 'trim|sanitize_string|lower_case',
            'status'   => 'trim|sanitize_string|lower_case'
        );
        // Gump rules for the input fields
        $rules = array(
            'ticket_id' => 'required|alpha_numeric|max_len,25|min_len,3',
            'status'   => 'required',
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
        // messages for the response
        $okMessage = 'Ticket Successfully closed!';

        $result = $crm->setTicketStatus($_POST);
        if ($result === false) {
            throw new Exception('Error Saving Ticket to the database');
        }

        $responseArray = array('type' => 'success', 'message' => $okMessage);
        $log->info('Successful', $_POST);

        $_POST = array();
    } catch (\Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
        $log->error('Failed', array($_POST, $e->getMessage()));
        $_POST = array();
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
