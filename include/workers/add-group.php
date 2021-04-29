<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use Dash\DashAuth;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);
$dashAuth = new DashAuth($pdo);


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (!isset($_POST['groupName'])) {
            throw new Exception('Please check the field again');
        }
        $filters = array(
            'groupName'   => 'trim|sanitize_string|lower_case'
        );
        $rules = array(
            'groupName'   => 'required|alpha_numeric|max_len,30',
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
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'add-group-form');
        if ($nonceTest === false) {
            throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }
        $test = $dashAuth->groupExists($_POST['groupName']);
        if ($test === true) {
            throw new Exception('This Group Already Exists');
        }
        $result = $dashAuth->createGroup($_POST['groupName']);
        if ($result === false) {
            throw new Exception('Unable to save, please refresh and try again');
        }

        $responseArray = array('type' => 'success', 'message' => 'Group Added Successfully');
    } catch (Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
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
