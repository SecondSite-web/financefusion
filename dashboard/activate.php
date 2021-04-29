<?php
/*
 *  Template Controller page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;

$template = "user_activate.twig";
$pagetitle = "User Activation";
$description = "Activate your user account here";
$class = "activate";

$pagepic = ""; // The featured image of the page default is a 1200 x 630 .png

$values = array(
    'page' => array(
        'title'         => $pagetitle,
        'description'   => $description,
        'class'         => $class,
        'pic'           => $pagepic
    )
);
echo $twig->render($template, $values);
