<?php
/**
 * File:          change-userrole.php
 * File Created:  2021/04/21 10:32
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

use Dash\DashAuth;

$dashAuth = new DashAuth($pdo);
/*
$result = $dashAuth->changeUserRole(1, 'admin');
if ($result === true) {
    echo 'true1';
}

$result2 = $dashAuth->changeUserGroup(1, 'dev');
if ($result2 === true) {
    echo 'true2';
}
/*
$result3 = $dashAuth->removeGroup('admin');
if ($result3 === true) {
    echo 'true3';
}
*/

$result3 = $dashAuth->groupExists('Admin');
if ($result3 === true) {
    echo 'true';
}

$users = $dashAuth->getAllUsers();
print_r($users);