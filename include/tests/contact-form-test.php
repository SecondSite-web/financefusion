<?php
/**
 * File:          contact-form-nonce.php
 * File Created:  2021/04/05 13:04
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

use Dash\Cf;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$cf = new Cf($pdo); // Call the contact form class

$nonceUtil = $dashNonce->nonceInit();
$nonce = $nonceUtil->create('contact-form');
$_POST = array(
    'firstname' => 'Test',
    'lastname' => 'Person',
    'phone' => '',
    'telephone' => '0799959818',
    'email' => 'gregory@realhost.co.za',
    'message' => 'required message',
    'status' => 'enquiry',
    'nonce' => $nonce
);


$log = new Logger('Contact Form');
$log->pushHandler(new StreamHandler(ROOT_PATH.'/logs/contact-form.log', Logger::INFO));


try {
    if (empty($_POST['nonce'])) {
        throw new Exception('missing nonce');
    }
    $nonceUtil = $dashNonce->nonceInit();
    $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'contact-form');
    if ($nonceTest === false) {
        throw new Exception('Aw, something went wrong :-( please refresh the page to submit again');
    }
    // the honeypot test first
    if (!empty($_POST['phone'])) {
        throw new Exception('I could not send the email.');
    }
    // the empty form test
    if (empty($_POST['firstname']) || empty($_POST['lastname'])) {
        throw new Exception('The form has not been filled in completely');
    }
    // Wixel Gump clean the $_POST array form input string santitisation to prevent code injection
    $filters = array(
        'firstname' => 'trim|sanitize_string|lower_case',
        'lastname'  => 'trim|sanitize_string|lower_case',
        'phone'     => 'trim|sanitize_string|lower_case',
        'telephone' => 'trim|sanitize_string|lower_case',
        'email'     => 'trim|sanitize_email|lower_case',
        'message'   => 'trim|sanitize_string|lower_case',
    );
    // Gump rules for the input fields
    $rules = array(
        'firstname' => 'required|alpha_numeric|max_len,25|min_len,3',
        'lastname'  => 'required|alpha_numeric|max_len,25|min_len,3',
        'phone'     => 'max_len,1',
        'telephone' => 'required|alpha_numeric|max_len,25|min_len,3',
        'email'     => 'required|valid_email',
        'message'   => 'required',
        'status'    => 'required',
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
    $okMessage = 'Contact form successfully submitted. Thank you, I will get back to you soon!';
    $errorMessage = 'There was an error while submitting the form. Please try again later';

    $mailer_result = $cf->cfMailer($_POST); // Mail the response using cf-class
    if ($mailer_result === false) {
        throw new Exception('Email Sending Failed');
    }

    $responseArray = array('type' => 'success', 'message' => $okMessage);
    $log->info('Successful', $_POST);

    $cf->cfDb($_POST);
    $_POST = array();
} catch (\Exception $e) {
    $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
    $log->error('Failed', array($_POST, $e->getMessage()));
    $_POST = array();
}

