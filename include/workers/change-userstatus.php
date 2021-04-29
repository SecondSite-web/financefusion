<?php
/**
 * File:          change-userstatus.php
 * File Created:  2021/04/21 13:17
 * Modified By:   Gregory Schoeman <gregory@secondsite.xyz>
 * PHP version 8.0
 * -----
 *
 * @category  WebApp
 * @package   NPM
 * @author    Gregory Schoeman <gregory@secondsite.xyz>
 * @copyright 2019-2021 SecondSite
 * @license   https://opensource.org/licenses/MIT  MIT
 * @version   GIT: <1.0.0>
 * @link      https://github.com/SecondSite-web/dash.git
 * @project   dash
 */

require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
require_once FUNCTIONS_URL.'/lock.php';

use Dash\DashAuth;

$dashAuth = new DashAuth($pdo);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'user_id' => 'trim|sanitize_string',
            'user_status' => 'trim|sanitize_string|lower_case'
        );
        $rules = array(
            'user_id' => 'required|alpha_numeric|max_len,20',
            'user_status' => 'required|alpha_numeric|max_len,20',
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
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'user_status');
        if ($nonceTest === false) {
            throw new \Exception('Aw, something went wrong :-( please refresh the page to submit again');
        }
        $user_id = $_POST['user_id'];
        $status = strtolower($_POST['user_status']);
        $result = $dashAuth->changeUserStatus($user_id, $status);
        if ($result === false) {
            throw new \Exception('Error writing to DB');
        }

        $okMessage = 'Setting Updated!';
        $errorMessage = 'Update Failed';
        $responseArray = array('type' => 'success', 'message' => $okMessage);
        // $_POST = array();
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
