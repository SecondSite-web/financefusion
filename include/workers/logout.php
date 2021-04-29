<?php
/**
 * File:          logout.php
 * File Created:  2021/04/19 09:41
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

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('User Login');
$log->pushHandler(new StreamHandler(ROOT_PATH . '/logs/login.log', Logger::INFO));

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);

try {
    $result = $auth->logout($_COOKIE['phpauth_session_cookie']);

    if ($result === true) {
        $responseArray = array('type' => 'success', 'message' => 'Logout Error');
        $log->info(
            'User Logout',
            array('User Logout Successful', $_COOKIE['phpauth_session_cookie'])
        );
        header("Location: ".SITE_URL."");
    } else {
        throw new \Exception('Logout Unsuccessful');
    }
} catch (\Exception $e) {
    $responseArray = array('type' => 'danger', 'message' => 'Logout Error');
    $log->error('User Logout Failed', array('User Logout Successful'));
}
