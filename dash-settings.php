<?php
use Dash\DashSetup;
use SebastianBergmann\Timer\Timer;

require_once ROOT_PATH . 'vendor/autoload.php'; // Composer Autoloader
if (file_exists(ROOT_PATH . 'tools/vendor/autoload.php')) {
    include_once ROOT_PATH . 'tools/vendor/autoload.php'; // Development tools - remove in production
}

require_once ROOT_PATH . 'include/functions/connect.php'; // Sql Connection
require_once ROOT_PATH . 'include/classes/classes.php'; // Dash PHP Classes
if (! defined('SMTP_EMAIL')) {
    $dashSetup = new dashSetup($pdo);
}
require_once ROOT_PATH . 'include/functions/functions.php'; // Calls all functions
