<?php
require_once __DIR__ . "/../../dash-loader.php";
use PHPAuth\Auth as PHPAuth;
use PHPAuth\Config as PHPAuthConfig;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);
/*
$userMeta = array(
    'firstname' => 'info',
    'lastname'  => 'Secondsite',
    'phone'     => '0799959818',
    'userrole'  => 'guest',
    'usergroup' => 'admin'
);
$result = $auth->register(
    'gregory@secondsite.xyz',
    'Tx5i6jfb7Iufn47Wx2QP',
    'Tx5i6jfb7Iufn47Wx2QP',
    $userMeta,
    $captcha_response = false,
    $use_email_activation = false
);
if ($result['error'] === true) {
    echo $result['message'];
}
$_POST = array(
    'send_mail' => '1'
);
$email = 'gregory@secondsite.xyz';
$password = randomString(10);
if ($_POST['send_mail'] === '1') {
    echo 'sending mail';
    $dashAuth->registrationMailer($email, $password);
}
*/
$result = $auth->requestReset('gregory@realhost.co.za', true);
print_r($result);