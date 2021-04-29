<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

use Dash\Cf;

$cf = new Cf($pdo);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'id' => 'trim|sanitize_string|lower_case',
            'status' => 'trim|sanitize_string|lower_case'
        );
        $rules = array(
            'id' => 'required|alpha_numeric|max_len,20',
            'status' => 'required|alpha_numeric',
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
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'cfStatus-form');
        if ($nonceTest === false) {
            throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }
        $id = $_POST['id'];
        $status = strtolower($_POST['status']);

        $result = $cf->setStatus($status, $id);
        if ($result === false) {
            throw new Exception('Error changing Status');
        }
        $okMessage = 'Setting Updated!';

        $responseArray = array('type' => 'success', 'message' => $okMessage);
    } catch (\Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
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
