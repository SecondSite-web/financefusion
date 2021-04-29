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
require_once __DIR__ . "/../../dash-loader.php";
use Dash\Crm;
use Dash\Cf;

$crm = new Crm($pdo);
$cf = new Cf($pdo);

foreach (range(0, 5) as $i) {
    echo $i." - ";

    $post = array(
        'user_id' => 1,
        'dest_group' => 17,
        'subject' => 'Problem finding something',
        'message' => 'Im having a problem locating the following files',
        'priority' => 'high',
        'status' => 'open'
    );
    $crm->saveTicket($post);

    usleep(10000);
    $meta = array(
        'ticket_id' => $i + 7,
        'user_id' => 4,
        'message' => 'Test Reply message',
    );
    $crm->saveMeta($meta);
    usleep(10000);
    $meta2 = array(
        'ticket_id' => $i + 7,
        'user_id' => 5,
        'message' => 'Test reply reply message',
    );
    $crm->saveMeta($meta2);

    $_POST = array(
        'firstname' => 'Greg',
        'lastname' => 'Schoeman',
        'telephone' => '0799959818',
        'email' => 'gregory@realhost.co.za',
        'message' => 'required message',
        'status' => 'enquiry'
    );
    $cf->cfDb($_POST);
}
