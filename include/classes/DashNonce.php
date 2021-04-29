<?php
/**
 * File:          DashNonce.php
 * File Created:  2021/04/04 10:24
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

namespace Dash;

use Nonce\CustomStore;
use Nonce\Nonce;

class DashNonce
{
    private object $pdo;

    /**
     * DashNonce constructor.
     * @param $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Build the Nonce Object
     * @return Nonce
     */
    public function nonceInit(): Nonce
    {
        $nonceConfig = $this->nonceConfig();
        // $nonceCookie = new \Nonce\HashStore\Cookie;
        $nonceStore = new CustomStore($this->pdo);
        return new Nonce($nonceConfig, $nonceStore);
    }

    /**
     * Setup the $nonceConfig variable
     * @return \Nonce\Config\Config
     */
    public function nonceConfig(): \Nonce\Config\Config
    {
        $nonceConfig = new \Nonce\Config\Config;

        $nonceConfig->setConfig('COOKIE_PATH', COOKIE_PATH);
        $nonceConfig->setConfig('COOKIE_DOMAIN', COOKIE_DOMAIN);
        $nonceConfig->setConfig('CSRF_COOKIE_NAME', CSRF_COOKIE_NAME);
        $nonceConfig->setConfig('CSRF_COOKIE_TTL', CSRF_COOKIE_TTL); // 2 hrs
        $nonceConfig->setConfig('RANDOM_SALT', RANDOM_SALT); // 32 bit SALT returns 256char hash
        $nonceConfig->setConfig('NONCE_HASH_CHARACTER_LIMIT', NONCE_HASH_CHARACTER_LIMIT);
        $nonceConfig->setConfig('TOKEN_HASHER_ALGO', TOKEN_HASHER_ALGO);
        $nonceConfig->setConfig('NONCE_DEFAULT_TTL', NONCE_DEFAULT_TTL); // 10 min
        $nonceConfig->setConfig('HASH_ID_CHARACTRER_LIMIT', HASH_ID_CHARACTRER_LIMIT);

        return $nonceConfig;
    }

    /**
     * Verifies a nonce call and deletes the nonce if verified
     *
     * @param $nonce
     * @param $action
     * @return bool
     */
    public function verifyNonce($nonce, $action): bool
    {
        $nonceUtil = $this->nonceInit();
        $nonceTest = $nonceUtil->verify($nonce, $action);
        if ($nonceTest === true) {
            $nonceUtil->delete($action);
            return true;
        }
        return false;
    }
}
