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

use Dash\Cf;

$cf = new Cf($pdo);
$status = 'replied';
$id = 1;
$result = $cf->setStatus($status, $id);
echo $result;
