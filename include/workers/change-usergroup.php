<?php

require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

use Dash\DashAuth;

$dashAuth = new DashAuth($pdo);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'usergroup' => 'trim|sanitize_string|lower_case',
            'user_id' => 'trim|sanitize_string'
        );
        $rules = array(
            'usergroup' => 'required|alpha_numeric|max_len,20',
            'user_id' => 'required|alpha_numeric|max_len,20',
            'nonce' => 'required'
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
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'user-group');
        if ($nonceTest === false) {
            throw new \Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }
        $userrole = strtolower($_POST['usergroup']);
        $user_id = $_POST['user_id'];

        $result = $dashAuth->changeUserGroup($user_id, $userrole);
        if ($result === false) {
            throw new \Exception('Error writing to DB');
        }

        $okMessage = 'Setting Updated!';
        $responseArray = array('type' => 'success', 'message' => $okMessage);
    } catch (\Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
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
