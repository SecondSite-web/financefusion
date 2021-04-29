<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('User Registration');
$log->pushHandler(new StreamHandler(ROOT_PATH.'/logs/password.log', Logger::INFO));

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'currpass'      => 'trim|sanitize_string',
            'newpass'       => 'trim|sanitize_string',
            'repeatnewpass' => 'trim|sanitize_string'
        );
        $rules = array(
            'currpass'      => 'required|max_len,100|min_len,8',
            'newpass'       => 'required|max_len,100|min_len,8',
            'repeatnewpass' => 'equalsfield,newpass',
            'nonce'         => 'required'
        );
        $validator = new GUMP();
        $whitelist = array_keys($rules);
        $_POST = $validator->sanitize($_POST, $whitelist);
        $_POST = $validator->filter($_POST, $filters);
        $validated = $validator->validate($_POST, $rules);
        if ($validated === false) {
            throw new \Exception($validator->get_readable_errors(true));
        }
        $nonceUtil = $dashNonce->nonceInit();
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'password-form');
        if ($nonceTest === false) {
            throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }
        $currpass = $_POST['currpass'];
        $newpass = $_POST['newpass'];
        $repeatnewpass = $_POST['repeatnewpass'];

        $uid= $auth->getCurrentUID();
        $result = $auth->changePassword($uid, $currpass, $newpass, $repeatnewpass);
        if ($result['error'] === true) {
            throw new \Exception($result['message']);
        }
        $responseArray = array('type' => 'success', 'message' => $result['message']);
        $log->info($result['message'], array('Email Address',  $auth->getCurrentUser()));
    } catch (\Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
        $log->error($e->getMessage(), array('Email Address',  $auth->getCurrentUser()));
    }

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
