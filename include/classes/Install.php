<?php

/**
 * File:          install.php
 * File Created:  2021/03/21 16:07
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
use PDOException;

class Install
{
    /**
     * Site wide debug setting for twig and Dash functions
     * @var bool
     */
    public bool $debug = true;
    /**
     * Allow registrations on the website
     * @var bool
     */
    public bool $allowRegister = true;

    /**
     * Smtp mailer email address
     * @var string
     */
    protected string $smtpEmail = 'info@secondsite.xyz';

    /**
     * The From name in an email from the website
     * @var string
     */
    public string $emailName = 'Dash Web Mailer';

    /**
     * Smtp Mailer password
     * @var string
     */
    protected string $smtpPass = 'vOw0nQDIL9A2bb8zTMv8EM1BZ';

    /**
     * Outgoing Mail Server name
     * @var string
     */
    protected string $smtpHost = 'rs13.cphost.co.za';

    /**
     * True or false for SMTP Mailer
     * @var string
     */
    public string $smtpAuth = 'true';

    /**
     * SSL/TLS or none for mail authentication
     * @var string
     */
    public string $smtpSecure = 'ssl';

    /**
     * Outgoing mailer port number
     * @var string
     */
    protected string $smtpPort =  '465';

    /**
     * Email standard subject line
     * @var string
     */
    public string $smtpSubject = 'Website Form Submission';

    /**
     * Mail CC setting
     * @var string
     */
    public string $smtpMailCc = '';

    /**
     * Mail Bcc setting
     * @var string
     */
    public string $smtpMailBcc = '';

    /**
     * The website Url
     * @var string
     */
    public string $siteUrl = 'http://127.0.0.1';

    /**
     * The name of the website of the business
     * @var string
     */
    public string $siteName = 'Dash PHP Framework';

    /**
     * Import the database connection object
     * @var object
     */
    protected object $pdo;

    /**
     * Initiate the Install array
     * @var array
     */
    private array $install;

    /**
     * Declare the setup table
     * @var string
     */
    public string $setupTable = 'dash_config';

    /**
     * Set the SMTP Debug mode - 0 off - 1 on
     * @var int
     */
    private int $smtpDebug = 0;

    /**
     * Enable or Disable PHPAuth mailer
     * @var int
     */
    private int $smtp = 1;

    private ?string $sitePasswordResetPage = 'dashboard/reset/';

    private ?string $siteActivationPage = 'dashboard/activate/';

    /**
     * Install constructor.
     * @param $pdo
     */
    public function __construct($pdo)
    {
        $this->install = array();
        $this->pdo = $pdo;
        $this->init();
        if (!$this->pdo->query("SHOW TABLES LIKE '{$this->setupTable}'")->fetchAll()) {
            $this->init();
        }
        $this->install = $this->pdo->query(
            "SELECT `setting`, `value` FROM {$this->setupTable}"
        )->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Setup::__get()
     *
     * @param mixed $setting
     * @return string|null
     */
    public function __get(mixed $setting): ?string
    {
        return $this->install[$setting] ?? null;
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
     * @param $setting
     * @return bool
     */
    public function __isset($setting): bool
    {
        if (array_key_exists($setting, $this->install)) {
            return true;
        }
        return false;
    }

    /**
     * Loads Methods
     */
    public function init(): void
    {
        $this->destroyTables();
        $this->phpauthConfig();
        $this->phpauthSettings();
        $this->phpauthAttempts();
        $this->phpauthRequests();
        $this->phpauthSessions();
        $this->phpauthUsers();
        $this->phpauthEmailsBanned();
        $this->dashConfig();
        $this->setSettings();
        $this->setAuthSetting();
    }

    /**
     * Deletes all tables
     */
    public function destroyTables(): void
    {
        $table = $this->setupTable;
        $this->pdo->exec('DROP TABLE IF EXISTS `phpauth_config`;');
        $this->pdo->exec('DROP TABLE IF EXISTS `phpauth_attempts`;');
        $this->pdo->exec('DROP TABLE IF EXISTS `phpauth_requests`;');
        $this->pdo->exec('DROP TABLE IF EXISTS `phpauth_sessions`;');
        $this->pdo->exec('DROP TABLE IF EXISTS `phpauth_users`;');
        $this->pdo->exec('DROP TABLE IF EXISTS `phpauth_emails_banned`;');
        $this->pdo->exec("DROP TABLE IF EXISTS $table;");
    }

    /**
     * Creates phpauth_config table
     */
    public function phpauthConfig(): void
    {
        $table = 'phpauth_config';
        try {
            $sql ="CREATE TABLE IF NOT EXISTS $table (
                      `setting` varchar(100) NOT NULL,
                      `value` varchar(100) DEFAULT NULL,
                      UNIQUE KEY `setting` (`setting`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    /**
     * Writes Settings to dash_config table
     */
    public function setSettings(): void
    {
        $this->__set('debug', $this->debug);
        $this->__set('smtpEmail', $this->smtpEmail);
        $this->__set('emailName', $this->emailName);
        $this->__set('smtpPass', $this->smtpPass);
        $this->__set('smtpHost', $this->smtpHost);
        $this->__set('smtpDebug', $this->smtpDebug);
        $this->__set('smtpAuth', $this->smtpAuth);
        $this->__set('smtpSecure', $this->smtpSecure);
        $this->__set('smtpPort', $this->smtpPort);
        $this->__set('smtpSubject', $this->smtpSubject);
        $this->__set('smtpMailCc', $this->smtpMailCc);
        $this->__set('smtpMailBcc', $this->smtpMailBcc);
        $this->__set('siteUrl', $this->siteUrl);
        $this->__set('siteName', $this->siteName);
        $this->__set('allowRegister', $this->allowRegister);
    }

    /**
     * Overwrites the most important phpauth settings with user settings
     */
    public function setAuthSetting(): void
    {
        $this->authSetting('site_email', $this->smtpEmail);
        $this->authSetting('site_key', 'fghuior.)/!/jdUkd8s2!7HVHG6$77ghg');
        $this->authSetting('site_name', $this->siteName);
        $this->authSetting('site_url', $this->siteUrl);
        $this->authSetting('smtp_debug', $this->smtpDebug);
        $this->authSetting('smtp', $this->smtp);
        $this->authSetting('smtp_auth', '1');
        $this->authSetting('smtp_host', $this->smtpHost);
        $this->authSetting('site_activation_page', $this->siteActivationPage);
        $this->authSetting('site_password_reset_page', $this->sitePasswordResetPage);
        $this->authSetting('smtp_password', $this->smtpPass);
        $this->authSetting('smtp_port', $this->smtpPort);
        $this->authSetting('smtp_security', $this->smtpSecure);
        $this->authSetting('smtp_username', $this->smtpEmail);
        $this->authSetting('recaptcha_enabled', 0);
        $this->authSetting('recaptcha_site_key', '');
        $this->authSetting('recaptcha_secret_key', '');
    }

    /**
     * PDO abstraction method to write settings to phpauth_config
     * @param $setting
     * @param $value
     */
    public function authSetting($setting, $value): void
    {
        $query = $this->pdo->prepare(
            "REPLACE INTO phpauth_config (setting, value) VALUES(?,?)",
            array($setting, $value)
        );
        $query->bindParam(1, $setting);
        $query->bindParam(2, $value);
        $query->execute();
    }

    /**
     * Populate phpauth_config table with all recommended settings
     */
    public function phpauthSettings(): void
    {
        $table = "phpauth_config";
        try {
            $sql ="INSERT INTO $table (`setting`, `value`) VALUES
              ('attack_mitigation_time',  '+30 minutes'),
              ('attempts_before_ban', '30'),
              ('attempts_before_verify',  '5'),
              ('bcrypt_cost', '10'),
              ('cookie_domain', NULL),
              ('cookie_forget', '+30 minutes'),
              ('cookie_http', '0'),
              ('cookie_name', 'phpauth_session_cookie'),
              ('cookie_path', '/'),
              ('cookie_remember', '+1 month'),
              ('cookie_secure', '0'),
              ('cookie_renew', '+5 minutes'),
              ('allow_concurrent_sessions', FALSE),
              ('emailmessage_suppress_activation',  '0'),
              ('emailmessage_suppress_reset', '0'),
              ('mail_charset','UTF-8'),
              ('password_min_score',  '3'),
              ('site_activation_page',  '/dashboard/activate'),
              ('site_activation_page_append_code', '0'), 
              ('site_email',  'admin@realhost.co.za' ),
              ('site_key',  'fghuior.)/!/jd458s2!7HVHG6577ghg'),
              ('site_name', 'PHPAuth'),
              ('site_password_reset_page',  '/dashboard/reset'),
              ('site_password_reset_page_append_code',  '0'),
              ('site_timezone', 'Africa/Johannesburg'),
              ('site_url',  'https://github.com/PHPAuth/PHPAuth'),
              ('site_language', 'en_GB'),
              ('smtp',  '0'),
              ('smtp_debug',  '0'),
              ('smtp_auth', '1'),
              ('smtp_host', 'smtp.example.com'),
              ('smtp_password', 'password'),
              ('smtp_port', '25'),
              ('smtp_security', NULL),
              ('smtp_username', 'email@example.com'),
              ('table_attempts',  'phpauth_attempts'),
              ('table_requests',  'phpauth_requests'),
              ('table_sessions',  'phpauth_sessions'),
              ('table_users', 'phpauth_users'),
              ('table_emails_banned', 'phpauth_emails_banned'),
              ('table_translations', 'phpauth_translation_dictionary'),
              ('verify_email_max_length', '100'),
              ('verify_email_min_length', '5'),
              ('verify_email_use_banlist',  '1'),
              ('verify_password_min_length',  '3'),
              ('request_key_expiration', '+10 minutes'),
              ('translation_source', 'php'),
              ('recaptcha_enabled', 0),
              ('recaptcha_site_key', ''),
              ('recaptcha_secret_key', ''),
              ('custom_datetime_format', 'Y-m-d H:i');" ;
            $this->pdo->exec($sql);
            // print("Settings written successfully.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    /**
     * Creates the phpauth_attempts table
     */
    public function phpauthAttempts(): void
    {
        try {
            $sql ="CREATE TABLE `phpauth_attempts` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `ip` char(39) NOT NULL,
              `expiredate` datetime NOT NULL,
              PRIMARY KEY (`id`),
              KEY `ip` (`ip`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    /**
     * Creates the phpauth_requests table
     */
    public function phpauthRequests(): void
    {
        $table = "phpauth_requests";
        try {
            $sql ="CREATE TABLE $table (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `uid` int(11) NOT NULL,
                  `token` CHAR(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                  `expire` datetime NOT NULL,
                  `type` ENUM('activation','reset') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `type` (`type`),
                  KEY `token` (`token`),
                  KEY `uid` (`uid`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    /**
     * creates the phpauth_sessions table
     */
    public function phpauthSessions(): void
    {
        $table = "phpauth_sessions";
        try {
            $sql ="CREATE TABLE $table (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `uid` int(11) NOT NULL,
              `hash` char(40) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
              `expiredate` datetime NOT NULL,
              `ip` varchar(39) NOT NULL,
              `device_id` varchar(36) DEFAULT NULL,
              `agent` varchar(200) NOT NULL,
              `cookie_crc` char(40) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    /**
     * Creates the phpauth_users table
     */
    public function phpauthUsers(): void
    {
        try {
            $sql ="CREATE TABLE `phpauth_users` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `email` varchar(255) DEFAULT NULL,
                  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
                  `isactive` tinyint(1) NOT NULL DEFAULT '0',
                  `firstname` varchar(255) DEFAULT NULL,
                  `lastname` varchar(255) DEFAULT NULL,
                  `phone` varchar(255) DEFAULT NULL,
                  `userrole` varchar(255) DEFAULT NULL,
                  `usergroup` varchar(255) DEFAULT NULL,
                  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `email` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    /**
     * Creates the phpauth_emails_banned table
     */
    public function phpauthEmailsBanned(): void
    {
        try {
            $sql ="CREATE TABLE `phpauth_emails_banned` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `domain` varchar(100) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    /**
     * Creates the dash_config table
     */
    public function dashConfig(): void
    {
        $table = "dash_config";
        try {
            $sql ="CREATE TABLE IF NOT EXISTS $table (
                      `setting` varchar(100) NOT NULL,
                      `value` varchar(100) DEFAULT NULL,
                      UNIQUE KEY `setting` (`setting`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}
