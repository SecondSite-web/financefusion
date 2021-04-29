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
$posts = new Post($pdo);

// foreach (range(0, 100000) as $i) {
$post = array(
    'post_title' => 'Contact Us',
    'post_content' => 'This is probably more for use in posts templates than in posts',
    'post_status' => 'published',
    'post_type' => 'page',
    'post_author' => '1',
    'post_url' => '/contact',
    'modified_time' => date('Y-m-d h:i:s')
);
$meta = array(
    array(
        'meta_key' => 'template',
        'meta_value' => 'contact_page.twig'
    ),
    array(
        'meta_key' => 'pageClass',
        'meta_value' => 'contact'
    ),
    array(
        'meta_key' => 'description',
        'meta_value' => 'This is the 150 character seo page description'),
    array(
        'meta_key' => 'postPic',
        'meta_value' => SITE_URL.'/img/page-img.png')
);
$posts->savePost($post, $meta);
// $post = [];
// $meta = [];
// }
