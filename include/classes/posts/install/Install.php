<?php
/**
 * File:          Install.php
 * File Created:  2021/04/05 23:16
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

namespace Dash\Post;

use PDOException;

class Install
{
    private object $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function init(): void
    {
        $this->createPostsTable();
        $this->createPostMetaTable();
    }
    public function createPostsTable(): void
    {
        try {
            $sql ="CREATE TABLE `dash_posts` (
                  `post_id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `post_title` varchar(100) DEFAULT NULL,
                  `post_content` longtext DEFAULT NULL,
                  `post_status` varchar(100) DEFAULT NULL,
                  `post_type` varchar(100) DEFAULT NULL,
                  `post_author` bigint(20) DEFAULT NULL,
                  `post_url` varchar(255) DEFAULT NULL,
                  `modified_time` datetime DEFAULT NULL,
                  `post_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`post_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" ;
            $this->pdo->exec($sql);
            print("Created dash_posts Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    public function createPostMetaTable(): void
    {
        try {
            $sql ="CREATE TABLE `dash_post_meta` (
                  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `post_id` bigint(20) NOT NULL,
                  `meta_key` varchar(255) DEFAULT NULL,
                  `meta_value` longtext DEFAULT NULL,
                  PRIMARY KEY (`meta_id`),
                  KEY `post_id` (`post_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" ;
            $this->pdo->exec($sql);
            print("Created dash_post_meta Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}
