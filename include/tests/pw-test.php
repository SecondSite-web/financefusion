<?php
/**
 * File:          add-post-test.php
 * File Created:  2021/04/11 16:47
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
use Dash\Post\Post;

require_once __DIR__ . "/../../dash-loader.php";

$password = randomString(10);
echo $password;