<?php
/**
 * File:          connect.php
 * File Created:  2021/03/21 15:36
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

try {
    $pdo = new PDO(DB_DRIVER.'', DB_USER, DB_PASSWORD);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    if ($e->getCode() === 1049) {
        try {
            $pdo = new PDO('mysql:host='.DB_HOST.';dbname=INFORMATION_SCHEMA', DB_USER, DB_PASSWORD);
            $stmt = $pdo->query(
                'CREATE DATABASE IF NOT EXISTS '.DB_NAME.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
            );
            $pdo->exec('USE '.DB_NAME.'');
        } catch (PDOException $e) {
            die('Error: '.$e->getMessage().' Code: '.$e->getCode());
        }
    } else {
        die('Error: '.$e->getMessage().' Code: '.$e->getCode());
    }
}
