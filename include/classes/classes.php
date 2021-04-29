<?php
/**
 * File:          classes.php
 * File Created:  2021/03/21 16:04
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
/*
 * Dash installer class - Setup
 */
require_once ROOT_PATH .'include/classes/Install.php';
/*
 * Class to call settings from Db and initiate constants
 */
require_once ROOT_PATH .'include/classes/DashSetup.php';
/*
 * Call the nonce functions class
 */
require_once ROOT_PATH .'include/classes/DashNonce.php';
/*
 * Class to implement a custom database store for nonces
 */
require_once ROOT_PATH .'include/classes/CustomStore.php';

require_once ROOT_PATH .'include/classes/DashAuth.php';
/*
 * Password encryption class
 */
require_once ROOT_PATH .'include/classes/Pw.php';
/*
 * The contact form class
 */
require_once ROOT_PATH .'include/classes/Cf.php';
/*
 * The contact form class
 */
require_once ROOT_PATH .'include/classes/Crm.php';
