<?php
/**
 * File:          nonce-test.php
 * File Created:  2021/04/04 00:08
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

$nonceUtil = $dashNonce->nonceInit();
$nonce = $nonceUtil->create('login-form2');
$nonceTest = $dashNonce->verifyNonce($nonce, 'login-form'); // Initiate Nonce Utility
echo $nonceTest;
