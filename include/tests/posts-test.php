<?php
/**
 * File:          posts-test.php
 * File Created:  2021/04/06 15:47
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
use SebastianBergmann\Timer\Timer;

require_once __DIR__ . "/../../dash-loader.php";
$posts = new Post($pdo);

    $timer = new Timer;
    $timer->start();
    $allPosts = $posts->getAllPosts();
    $allMeta = $posts->getAllMeta();
    $postId = "30";
    foreach ($allPosts as $single) {
        if ($single['post_id'] === $postId) {
            $thisPost[] = $single;
        }
    }
    $thisMeta = array();
    foreach ($allMeta as $loop) {
        if ($loop['post_id'] === $postId) {
            $thisMeta[] = $loop;
        }
    }
    $duration = $timer->stop();
    var_dump($duration->asNanoseconds());
    $timer->start();
    $post = $posts->getPostById(29);
    $meta = $posts->getPostMetaById(29);
    $duration = $timer->stop();
    var_dump($duration->asNanoseconds());
// print_r($thisMeta);
// print_r($thisMeta);
// print_r($allPosts);
// print_r($allMeta);




// print_r($post);
// print_r($meta);
