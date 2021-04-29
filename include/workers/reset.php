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
$log = new Logger('Reset Password');
$log->pushHandler(new StreamHandler(ROOT_PATH.'/logs/password.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'key' => 'trim|sanitize_string',
            'password'    => 'trim|sanitize_string',
            'confirm_password' => 'trim|sanitize_string'
        );
        $rules = array(
            'key' => 'required|alpha_numeric|max_len,50|min_len,10',
            'password'  => 'required|max_len,100|min_len,8',
            'confirm_password'  => 'equalsfield,password',
            'nonce'     => 'required'
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
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'reset-form');
        if ($nonceTest === false) {
            throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }
        $key = $_POST['key'];
        $password = $_POST['password'];
        $repeatpassword = $_POST['confirm_password'];
        $result = $auth->resetPass($key, $password, $repeatpassword);

        if ($result['error'] === true) {
            throw new \Exception($result['message']);
        }
        $responseArray = array('type' => 'success', 'message' => $result['message']);
        $log->info('Password reset Successful', array('Message',  $result['message']));
    } catch (\Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
        $log->error('Password reset Failed', array('Message', $e->getMessage()));
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
