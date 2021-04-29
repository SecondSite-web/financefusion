<?php
/**
 * File:          DashAuth.php
 * File Created:  2021/04/20 12:42
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
use PHPAuth\Auth as PHPAuth;
use PHPAuth\Config as PHPAuthConfig;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class DashAuth
{
    private PDO $pdo;
    /**
     * @var PHPAuth
     */
    private PHPAuth $auth;
    private string $groupTable = 'dash_user_groups';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $config = new PHPAuthConfig($this->pdo);
        $this->auth = new PHPAuth($this->pdo, $config);
        if (!$this->pdo->query("SHOW TABLES LIKE '{$this->groupTable}'")->fetchAll()) {
            $this->createGroupTable();
        }
    }

    /**
     * Get the current users details and sets them as globals
     * @return array|bool
     */
    public function sessionUser(): array|bool
    {
        if ($this->auth->isLogged()) {
            $uid = $this->auth->getCurrentUID();
            return $this->auth->getUser($uid, false);
        }
        return false;
    }

    public function getAllUsers()
    {
        return $this->pdo->query(
            "SELECT `id`, `firstname`, `lastname`, `phone`, `email`, `usergroup`, `userrole` FROM phpauth_users"
        )->fetchAll(pdo::FETCH_ASSOC);
    }

    public function getUserCount()
    {
        $table = 'phpauth_users';
        $sql = "SELECT count(*) FROM {$table} WHERE NOT isactive = ?";
        $result = $this->pdo->prepare($sql);
        $result->execute([0]);
        return $result->fetchColumn();
    }

    public function userGroupCount($group)
    {
        $table = 'phpauth_users';
        $sql = "SELECT count(*) FROM {$table} WHERE usergroup = ? AND NOT isactive = ? ";
        $result = $this->pdo->prepare($sql);
        $result->execute([$group, '0']);
        return $result->fetchColumn();
    }

    public function getAllCounts($groups): array
    {
        $bar = [];
        foreach ($groups as $group) {
            $result = $this->userGroupCount($group['id']);
            $foo = array(
                'group' => $group['user_group'],
                'count' => $result
            );
            $bar[] = $foo;
        }
        return $bar;
    }

    public function getEmail($uid)
    {
        try {
            $sql = "SELECT `email` FROM phpauth_users WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$uid]);
            return $stmt->fetch(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return 'error retrieving email address';
        }
    }

    public function getEmailsByGroupId($groupId)
    {
        try {
            $sql = "SELECT `email` FROM phpauth_users WHERE usergroup=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$groupId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return 'error retrieving email address';
        }
    }

    public function changeUserRole($userId, $userRole): bool
    {
        try {
            $sql = "UPDATE phpauth_users SET userrole=? WHERE id=?";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute([$userRole, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function changeUserGroup($userId, $userGroup): bool
    {
        try {
            $sql = "UPDATE phpauth_users SET usergroup=? WHERE id=?";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute([$userGroup, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    public function changeUserEmail($userId, $email): bool
    {
        try {
            $sql = "UPDATE phpauth_users SET email=? WHERE id=?";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute([$email, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    public function changeUserStatus($userId, $isActive): bool
    {
        try {
            $sql = "UPDATE phpauth_users SET isactive=? WHERE id=?";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute([$isActive, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    public function createGroupTable(): void
    {
        try {
            $sql ="CREATE TABLE {$this->groupTable} (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_group` varchar(100) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
    public function createGroup($user_group): bool
    {
        $value = strtolower($user_group);
        try {
            $query = $this->pdo->prepare(
                "REPLACE INTO {$this->groupTable} (user_group) VALUES(?)",
                array($user_group)
            );
            $query->bindParam(1, $value);
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function removeGroup($user_group): bool
    {
        try {
            $table = $this->groupTable;
            $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE user_group=:user_group");
            $stmt->execute(['user_group' => $user_group]);
            $stmt->fetch();
            return true;
        } catch (PDOException) {
            return false;
        }
    }
    public function groupExists($groupName)
    {
        $groups = $this->getGroups();
        $groupSml = strtolower($groupName);
        foreach ($groups as $group) {
            if (in_array($groupSml, $group, true)) {
                return true;
            }
        }
        return false;
    }
    public function getGroups(): array
    {
        $table = $this->groupTable;
        return $this->pdo->query(
            "SELECT * FROM {$table}"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupById($groupId): mixed
    {
        $table = $this->groupTable;
        $stmt = $this->pdo->prepare("SELECT `user_group` FROM {$table} WHERE id=:id");
        $stmt->execute(['id' => $groupId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRoles()
    {
        $table = 'phpauth_users';
        return $this->pdo->query(
            "SELECT DISTINCT userrole FROM {$table}"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrationMailer($email, $password): bool|string
    {
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions

        try {
            //Server settings
            $mail->SMTPDebug = SMTP_DEBUG;                                 // Enable verbose debug output
            $mail->isSMTP();
            date_default_timezone_set('Africa/Johannesburg');   // Set mailer to use SMTP
            $mail->Host = SMTP_HOST;                         // Specify main and backup SMTP servers
            $mail->SMTPAuth = SMTP_AUTH;                               // Enable SMTP authentication
            $mail->Username = SMTP_EMAIL;                           // SMTP username
            $mail->Password = SMTP_PASS;                           // SMTP password
            $mail->SMTPSecure = SMTP_SSL;            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = SMTP_PORT;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom(SMTP_EMAIL, SMTP_NAME);
            $mail->addAddress($email, SMTP_NAME);              // Add a recipient
            $mail->addReplyTo(SMTP_EMAIL, SMTP_NAME);
            /*
            if (null !== $dashSetup->mailcc) {
                $mail->addCC($this->mailcc);
            }
            if (null !== $this->mailbcc) {
                $mail->addBCC($this->mailbcc);
            }
            */
            /* Attachments
            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            */
            //Content
            $mail->isHTML(true);
            $mail->Subject = SMTP_SUBJECT;

            $message = file_get_contents(ROOT_PATH.'./email-templates/welcome.html');
            $message = str_replace(array('%email%', '%password%'), array($email, $password), $message);
            $mail->MsgHTML($message);
            $mail->AltBody = strip_tags($message);
            $mail->send();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
