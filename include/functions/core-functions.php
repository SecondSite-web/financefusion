<?php
/**
 * File:          core-functions.php
 * File Created:  2021/04/05 19:45
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

/**
 * Hack function - not in use
 * Returns the current url
 * @return string
 */
function currentUrl(): string
{
    ob_start();
    if (!isset($_SERVER['HTTP_HOST'])) {
        $_SERVER = array('HTTPS' => 'off', 'HTTP_HOST' => '127.0.0.1', 'REQUEST_URI' => '/');
    }
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' .
        $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    ob_end_flush();
    return $link;
}
function randomString($length)
{
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $number = random_int(0, 36);
        $character = base_convert($number, 10, 36);
        $random_string .= $character;
    }

    return $random_string;
}
