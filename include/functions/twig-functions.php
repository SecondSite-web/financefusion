<?php
/**
 * File:          twig-functions.php
 * File Created:  2021/03/29 16:52
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

use Dash\DashNonce;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Dash\DashAuth;

/*
 * Template Directories
 */
$templatePaths = array(
    ROOT_PATH ."/templates/website",
    ROOT_PATH ."/templates/dashboard",
    ROOT_PATH ."/templates/auth",
    ROOT_PATH ."/templates/crm"
);
$loader = new FilesystemLoader($templatePaths);

/*
 * Environment Loader
 */
$twig = new Environment($loader, array(
    'debug' => true,
    # 'cache' => 'cache',
));

/*
 * Custom Twig function to call a nonce from client side
 * <input type="hidden" name="nonce" value="{{ call_nonce('contact-form') }}" />
 */
$dashNonce = new DashNonce($pdo);
$nonce_function = new TwigFunction(
    'call_nonce',
    function ($action_name, $script = false) use ($dashNonce) {
        ob_start();
        $nonceUtil = $dashNonce->nonceInit();
        $nonce = $nonceUtil->create($action_name);
        ob_end_flush();
        return $nonce;
    }
);
$twig->addFunction($nonce_function);

/*
 * Setup the site globals for all Twig Templates
 */
$twig_globals = [
    'name' => SITE_NAME,
    'url' => SITE_URL,
    'year' => date('y'),
    'worker' => WORKERS_URL,
    'img' => IMG_URL,
    'admin' => ADMIN_URL
];
$twig->addGlobal('site', $twig_globals);
$dashAuth = new DashAuth($pdo);
$twig->addGlobal('user', $dashAuth->sessionUser());
/*
 * Debug extension for Twig templating
 */
$twig->addExtension(new DebugExtension());
