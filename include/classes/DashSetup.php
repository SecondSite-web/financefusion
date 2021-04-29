<?php
/**
 * File:          DashSetup.php
 * File Created:  2021/04/02 23:23
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

use PDO;

class DashSetup
{
    private object $pdo;
    protected array $dashSetup;
    protected string $setupTable = 'dash_config';


    public function __construct($pdo)
    {
        $this->dashSetup = array();
        $this->pdo = $pdo;

        if (!$this->pdo->query("SHOW TABLES LIKE '{$this->setupTable}'")->fetchAll()) {
            new Install($this->pdo);
        }
        $this->dashSetup = $this->pdo->query(
            "SELECT `setting`, `value` FROM {$this->setupTable}"
        )->fetchAll(PDO::FETCH_KEY_PAIR);
        $this->setConstants();
    }

    /**
     * Setup::__get()
     *
     * @param mixed $setting
     * @return string|null
     */
    public function __get(mixed $setting): ?string
    {
        return $this->dashSetup[$setting] ?? null;
    }

    /**
     * Setup::__set()
     *
     * @param mixed $setting
     * @param mixed $value
     */
    public function __set(mixed $setting, mixed $value)
    {
        $query = $this->pdo->prepare(
            "REPLACE INTO {$this->setupTable} (setting, value) VALUES(?,?)",
            array($setting, $value)
        );
        $query->bindParam(1, $setting);
        $query->bindParam(2, $value);
        $query->execute();
    }


    /**
     * Checks if a field is set in the array
     * @param $setting
     * @return bool
     */
    public function __isset($setting): bool
    {
        if (array_key_exists($setting, $this->dashSetup)) {
            return true;
        }
        return false;
    }

    /**
     * Sets constants from Db settings
     */
    public function setConstants(): void
    {
        $vars = $this->dashSetup;
        // SMTP Mailer Constants
        define('SMTP_AUTH', $vars['smtpAuth']);
        define('SMTP_DEBUG', $vars['smtpDebug']);
        define('SMTP_EMAIL', $vars['smtpEmail']);
        define('SMTP_NAME', $vars['emailName']);
        define('SMTP_HOST', $vars['smtpHost']);
        define('SMTP_PASS', $vars['smtpPass']);
        define('SMTP_PORT', $vars['smtpPort']);
        define('SMTP_SSL', $vars['smtpSecure']);
        define('SMTP_SUBJECT', $vars['smtpSubject']);
        // Site Globals
        define('SITE_URL', $vars['siteUrl']);
        define('SITE_NAME', $vars['siteName']);
        define('FUNCTIONS_URL', ROOT_PATH.'/include/functions');
        define('WORKERS_URL', SITE_URL.'/include/workers');
        define('IMG_URL', SITE_URL.'/img');
        define('ADMIN_URL', SITE_URL.'/dashboard');
    }
}
