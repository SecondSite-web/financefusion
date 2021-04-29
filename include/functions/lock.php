<?php
/**
 * File:          lock.php
 * File Created:  2021/04/18 11:20
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

use Dash\DashAuth;

$dashAuth = new DashAuth($pdo);
$user = $dashAuth->sessionUser();

if ($user === false) {
    header("Location: ".SITE_URL."");
    exit();
}
if (($user['userrole'] === 'banned') || ($user['isactive'] === 0)) {
    header("Location: ".SITE_URL."");
    exit();
}
