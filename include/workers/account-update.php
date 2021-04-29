<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);

$log = new Logger('User Registration');
$log->pushHandler(new StreamHandler(ROOT_PATH.'/logs/register.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (!isset($_POST['email'])) {
            throw new Exception('Please check the email address');
        }
        $filters = array(
            'user_id'     => 'trim|sanitize_string',
            'firstname'   => 'trim|sanitize_string',
            'lastname'    => 'trim|sanitize_string',
            'phone'       => 'trim|sanitize_string|lower_case',
            'email'       => 'trim|sanitize_email|lower_case'
        );
        $rules = array(
            'user_id'     => 'required|alpha_numeric|max_len,100|min_len,3',
            'firstname'   => 'required|alpha_numeric|max_len,100|min_len,3',
            'lastname'    => 'required|alpha_numeric|max_len,100|min_len,3',
            'phone'       => 'required|alpha_numeric|max_len,100|min_len,3',
            'email'       => 'required|valid_email',
            'nonce'         => 'required'
        );

        $validator = new GUMP();
        $whitelist = array_keys($rules);
        $_POST = $validator->sanitize($_POST, $whitelist);
        $_POST = $validator->filter($_POST, $filters);
        $validated = $validator->validate($_POST, $rules);
        if ($validated === false) {
            throw new Exception($validator->get_readable_errors(true));
        }
        $nonceUtil = $dashNonce->nonceInit();
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'user-account-form');
        if ($nonceTest === false) {
            throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }

        $email = $_POST['email'];
        $uid = $_POST['user_id'];
        $params = array(
            'firstname' => $_POST['firstname'],
            'lastname' => $_POST['lastname'],
            'phone' => $_POST['phone'],
        );
        $result = $auth->updateUser($uid, $params);

        if ($result['error'] === '1') {
            throw new Exception($result['message']);
        }
        $originEmail = $dashAuth->getEmail($uid);
        if ($originEmail !== $email) {
            $emailTest = $auth->isEmailTaken($email);
            if ($emailTest === true) {
                throw new Exception('This email address is already in use');
            }
            $emailBanned = $auth->isEmailBanned($email);
            if ($emailBanned === true) {
                throw new Exception('This email address has been banned, Please contact an admin');
            }
            $emailUpdated = $dashAuth->changeUserEmail($uid, $email);
            if ($emailUpdated === false) {
                throw new Exception('Failed to update email address');
            }
        }
        $responseArray = array('type' => 'success', 'message' => $result['message']);
        $log->info(
            $result['message'],
            array(
                'Email',
                $_POST['email']." | User: ".$_POST['firstname']." ".$_POST['lastname']
            )
        );
    } catch (Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
        $log->error(
            $e->getMessage(),
            array(
                'Email',
                $_POST['email']." | User: ".$_POST['firstname']." ".$_POST['lastname']
            )
        );
    }

    // if requested by AJAX request return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        $encoded = json_encode($responseArray);
        header('Content-Type: application/json');
        echo $encoded;
    } else {
        echo $responseArray['message'];
    }
} else {
    header("Location: /");
}
