<?php
/**
 * File:          Pw.php
 * File Created:  2021/04/03 12:05
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

class Pw
{

    /**
     * Class static
     *
     * @var Pw|null
     */
    private static ?Pw $instance = null;

    /**
     * selected algorithm
     *
     * @var int
     */
    private int $algorithm;

    /**
     * PASSWORD_ARGON2I OR PASSWORD_ARGON2ID constant
     *
     * @constant int
     */
    public const ARGON2I = 2;

    public const ARGON2ID = 3;

    /**
     * PASSWORD_BCRYPT constant
     *
     * @constant int
     */
    public const BCRYPT = 1;

    /**
     * PASSWORD_ARGON2I or PASSWORD_ARGON2ID default options
     *
     * @var array
     */
    private array $defaultArgon2Options = [
        'memory_cost' => 1024,
        'time_cost' => 2,
        'threads' => 2,
    ];

    /**
     * PASSWORD_BCRYPT default options
     *
     * @var array
     */
    private array $defaultBcryptOptions = [
        'cost' => 10,
    ];

    /**
     * selected options
     *
     * @var array
     */
    private array $options = [];

    /**
     * @return Pw
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *
     * @param int $algorithm
     * @param array $options
     * @return $this
     */
    public function initialize(int $algorithm = 0, $options = array()): self
    {
        if (PHP_VERSION >= 7.3) {
            $this->algorithm = !empty($algorithm) ? $algorithm : self::ARGON2ID;
            $this->options = !empty($options) ? $options : $this->defaultArgon2Options;
        } elseif (PHP_VERSION >= 7.2 && PHP_VERSION < 7.3) {
            $this->algorithm = (!empty($algorithm) && $algorithm !== self::ARGON2ID) ? $algorithm : self::ARGON2I;
            $this->options = !empty($options) ? $options : $this->defaultArgon2Options;
        } else {
            $this->algorithm = self::BCRYPT;
            $this->options = !empty($options) ? $options : $this->defaultBcryptOptions;
        }

        return $this;
    }

    /**
     * creates a new password hash using a strong one-way hashing algorithm
     * @param string $password
     * @return string
     */
    public function hash(string $password): string
    {
        return password_hash($password, $this->algorithm, $this->options);
    }

    /**
     * Verifies that a password matches a hash
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validate(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Returns information about the given hash
     * @param string $hash
     * @return array
     */
    public function getInfo(string $hash): array
    {
        return password_get_info($hash);
    }

    /**
     * @return int
     */
    public function getAlgorithm(): int
    {
        return $this->algorithm;
    }

    /**
     * @param int $algorithm
     */
    public function setAlgorithm(int $algorithm): void
    {
        if (PHP_VERSION >= 7.3) {
            $this->algorithm = !empty($algorithm) ? $algorithm : self::ARGON2ID;
        } elseif (PHP_VERSION >= 7.2 && PHP_VERSION < 7.3) {
            $this->algorithm = (!empty($algorithm) && $algorithm !== self::ARGON2ID) ? $algorithm : self::ARGON2I;
        } else {
            $this->algorithm = self::BCRYPT;
        }
    }
}
