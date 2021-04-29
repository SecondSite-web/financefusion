<?php
/**
 * File:          CustomStore.php
 * File Created:  2021/04/03 21:58
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

namespace Nonce;

use Nonce\HashStore\Store;
use PDO;
use PDOException;

/**
 * @property  customStore
 */
class CustomStore implements Store
{
    /**
     * Database Connection
     * @var object
     */
    private object $pdo;

    /**
     * Initialise array for all nonces in Db
     * @var array
     */
    private array $nonces;


    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        if (!$this->pdo->query("SHOW TABLES LIKE 'dash_nonce'")->fetchAll()) {
            $this->storeTable();
        }
        $this->nonces = $this->pdo->query(
            "SELECT * FROM dash_nonce"
        )->fetchAll(PDO::FETCH_ASSOC);
        date_default_timezone_set('Africa/Johannesburg');
        $this->isExpired();
    }
    /**
     * Store a key temporarily
     *
     * @param string $name key to be stored
     * @param string $value value to be stored for the given key
     * @param int $expire_seconds expire the data after X seconds (data TTL)
     * @return bool success/failure
     */
    public function setKey(string $name, string $value, int $expire_seconds = 0): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'REPLACE INTO dash_nonce (name, value, expire)
    VALUES (:name, :value, :expire)'
            );
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':expire', $expire_seconds);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get a key from temporary storage
     *
     * @param string $name key to be retrieved
     * @return string value for stored key or empty string on key unavailable
     */

    public function getKey(string $name): string
    {
        return $name;
    }

    /**
     * Unset a key from temporary storage
     *
     * @param string $name key to be removed
     * @return bool success/failure
     */

    public function deleteKey(string $name): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM dash_nonce WHERE name=:name');
            $stmt->execute(['name' => $name]);
            $stmt->fetch();
            return true;
        } catch (PDOException) {
            return false;
        }

    }

    public function storeTable(): void
    {
        try {
            $sql ="CREATE TABLE `dash_nonce` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(100) DEFAULT NULL,
                  `value` varchar(100) DEFAULT NULL,
                  `expire` varchar(100) DEFAULT NULL,
                  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `name` (`name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    /**
     * Deletes Expired Nonces
     */
    public function isExpired(): void
    {
        $nonces = $this->nonces;
        foreach ($nonces as $nonce) {
            $nonceDt = $nonce['dt'];
            $dt = strtotime($nonceDt);
            $var = NONCE_DEFAULT_TTL;
            $max = $dt + $var;
            $nowDt = date("Y-m-d H:i:s");
            $now = strtotime($nowDt);
            if ($max <= $now) {
                $this->deleteKey($nonce['name']);
            }
        }
    }
}
