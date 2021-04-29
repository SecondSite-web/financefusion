<?php
/*
 *  Template Controller page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;

$template = "user_reset.twig";

$values = array(
    'page' => array(
        'title'         => "Reset Password",
        'description'   => "Reset Password Pages",
        'class'         => "reset-password",
        'pic'           => ""
    )
);
echo $twig->render($template, $values);
