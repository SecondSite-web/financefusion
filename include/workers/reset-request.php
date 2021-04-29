<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('Password Reset Request');
$log->pushHandler(new StreamHandler(ROOT_PATH.'/logs/password.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'email' => 'trim|sanitize_email'
        );
        $rules = array(
            'email' => 'required|valid_email',
            'nonce' => 'required',
        );
        $validator = new GUMP();
        $whitelist = array_keys($rules);
        $_POST = $validator->sanitize($_POST, $whitelist);
        $_POST = $validator->filter($_POST, $filters);
        $validated = $validator->validate($_POST, $rules);
        // $_POST = $validator->run($_POST);
        if ($validated === false) {
            throw new \Exception($validator->get_readable_errors(true));
        }
        $nonceUtil = $dashNonce->nonceInit();
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'reset-request-form');
        if ($nonceTest === false) {
            throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }
        $email = $_POST['email'];

        $result = $auth->requestReset($email, $use_email_activation = true);

        if ($result['error'] === true) {
            throw new \Exception($result['message']);
        }
        $responseArray = array('type' => 'success', 'message' => $result['message']);
        $log->info('Successful', array('Email', $_POST['email']." ". $result['message']));
    } catch (\Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
        $log->error('Failed', array('Email', $_POST['email']." ". $e->getMessage()));
    }

    // if requested by AJAX request return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $encoded = json_encode($responseArray);
        header('Content-Type: application/json');
        echo $encoded;
    } else {
        echo $responseArray['message'];
    }
} else {
    header("Location: /");
}
