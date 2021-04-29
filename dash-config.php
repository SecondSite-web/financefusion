<?php

/* Debug Mode */
define('DASH_DEBUG', true);

/* The name of the database */
define('DB_NAME', 'ravapp_dashmaster');

/* MySQL database username */
define('DB_USER', 'ravapp_dashuser');  // ravapp_dashuser

/* MySQL database password */
define('DB_PASSWORD', 'OFJw]NfCCMjp'); // OFJw]NfCCMjp

/* MySQL hostname */
define('DB_HOST', 'localhost');

/* MySQL port */
define('DB_PORT', 3306);

/* Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/* Compiled sql Db driver statement */
define('DB_DRIVER', 'mysql:host='.DB_HOST.';dbname='.DB_NAME);

// Nonce Settings
define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '127.0.0.1');
define('CSRF_COOKIE_NAME', 'CSRF');
define('CSRF_COOKIE_TTL', 7200);
define('RANDOM_SALT', 'TjxA$b,Lo$mjqU|T#x?HdnJ1.dREjkM|');
define('NONCE_HASH_CHARACTER_LIMIT', 22);
define('TOKEN_HASHER_ALGO', 'sha512');
define('NONCE_DEFAULT_TTL', 600);
define('HASH_ID_CHARACTRER_LIMIT', 11);

if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/');
}
