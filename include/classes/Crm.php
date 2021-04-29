<?php
/**
 * File:          Crm.php
 * File Created:  2021/04/23 21:52
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
use PHP_CodeSniffer\Tests\Core\File\testFECNNamespacedClass;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Crm
{
    private object $pdo;
    private string $crmTable = 'dash_tickets';
    private string $crmMetaTable = 'dash_ticket_meta';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        if (!$this->pdo->query("SHOW TABLES LIKE '{$this->crmTable}'")->fetchAll()) {
            $this->createTable();
            $this->createCrmMetaTable();
        }
        date_default_timezone_set('Africa/Johannesburg');
    }

    public function groupTicketCount($group)
    {
        $table = $this->crmTable;
        $sql = "SELECT count(*) FROM {$table} WHERE dest_group = ? AND NOT status = ? ";
        $result = $this->pdo->prepare($sql);
        $result->execute([$group, 'closed']);
        return $result->fetchColumn();
    }

    public function getAllCounts($groups): array
    {
        $bar = [];
        foreach ($groups as $group) {
            $result = $this->groupTicketCount($group['id']);
            $foo = array(
                'group' => $group['user_group'],
                'count' => $result
            );
            $bar[] = $foo;
        }
        return $bar;
    }

    public function openTicketCount($status)
    {
        $table = $this->crmTable;
        $sql = "SELECT count(*) FROM {$table} WHERE NOT status = ?";
        $result = $this->pdo->prepare($sql);
        $result->execute([$status]);
        return $result->fetchColumn();
    }


    public function modifyTicket($values): bool
    {
        try {
            $table = $this->crmTable;
            $sql = "UPDATE {$table} SET dest_group=:dest_group, priority=:priority, status=:status 
                    WHERE ticket_id=:ticket_id";
            $stmt= $this->pdo->prepare($sql);
            $stmt->bindvalue('dest_group', $values['dest_group']);
            $stmt->bindParam('priority', $values['priority']);
            $stmt->bindParam('status', $values['status']);
            $stmt->bindvalue('ticket_id', $values['ticket_id']);
            $stmt->execute();
            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function setTicketStatus($fields): bool
    {
        $table = $this->crmTable;
        $sql = "UPDATE {$table} SET status=? WHERE ticket_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([$fields['status'], $fields['ticket_id']]);
    }

    public function getAllTickets()
    {
        $table = $this->crmTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} ORDER BY `date` DESC, `ticket_id` DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllMeta()
    {
        $table = $this->crmMetaTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} ORDER BY `date` DESC, `meta_id` DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketById($ticketId)
    {
        $table = $this->crmTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE ticket_id=:ticket_id");
        $stmt->execute(['ticket_id' => $ticketId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTicketMetaById($ticketId): mixed
    {
        $table = $this->crmMetaTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE ticket_id=:ticket_id");
        $stmt->execute(['ticket_id' => $ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveTicketMeta($meta): bool
    {
        foreach ($meta as $row) {
            $this->saveMeta($row);
        }
        return true;
    }

    public function saveMeta($meta): bool
    {
        $table = $this->crmMetaTable;
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$table} (ticket_id, user_id, message)
                VALUES (:ticket_id, :user_id, :message)"
            );
            $stmt->bindParam(':ticket_id', $meta['ticket_id']);
            $stmt->bindParam(':user_id', $meta['user_id']);
            $stmt->bindParam(':message', $meta['message']);
            $stmt->execute();
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    public function saveTicket(array $fields): bool
    {
        $table = $this->crmTable;

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$table} (user_id, dest_group, subject, message, priority, status)
    VALUES (:user_id, :dest_group, :subject, :message, :priority, :status)"
            );
            $stmt->bindParam(':user_id', $fields['user_id']);
            $stmt->bindParam(':dest_group', $fields['dest_group']);
            $stmt->bindParam(':subject', $fields['subject']);
            $stmt->bindParam(':message', $fields['message']);
            $stmt->bindParam(':priority', $fields['priority']);
            $stmt->bindParam(':status', $fields['status']);
            $stmt->execute();
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    public function sendMail($fields, $mailList): bool|string
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
            $mail->setFrom($fields['email'], $fields['name']);


            foreach ($mailList as $emails) {
                $mail->addAddress($emails);
            }
            $mail->addCC(SMTP_EMAIL, SMTP_NAME);              // Add a recipient
            $mail->addReplyTo(SMTP_EMAIL, SMTP_NAME);

            $mail->isHTML(true);
            $mail->Subject = $fields['subject'];

            $message = file_get_contents(ROOT_PATH.'./email-templates/open-ticket.html');
            $message = str_replace(
                array(
                    '%title%','%name%', '%email%', '%group%', '%status%', '%priority%', '%message%', '%siteUrl%'
                ),
                array(
                    $fields['title'],
                    $fields['name'],
                    $fields['email'],
                    $fields['group'],
                    $fields['status'],
                    $fields['priority'],
                    $fields['message'],
                    SITE_URL
                ),
                $message
            );
            $mail->MsgHTML($message);
            $mail->AltBody = strip_tags($message);
            $mail->send();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function createCrmMetaTable(): void
    {
        $table = $this->crmMetaTable;
        try {
            $sql ="CREATE TABLE IF NOT EXISTS {$table} (
                  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `ticket_id` bigint(20) NOT NULL,
                  `user_id` varchar(255) DEFAULT NULL,
                  `message` longtext DEFAULT NULL,
                  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,           
                  PRIMARY KEY (`meta_id`),
                  KEY `ticket_id` (`ticket_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" ;
            $this->pdo->exec($sql);
            // print("Created dash_post_meta Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    public function createTable(): void
    {
        $table = $this->crmTable;
        try {
            $sql ="CREATE TABLE IF NOT EXISTS $table (
                `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `dest_group` int(11) NOT NULL,
                `subject` varchar(255) NOT NULL,
                `message` text NOT NULL,
                `priority` varchar(255) NOT NULL,
                `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,           
                `status` varchar(20) NOT NULL,
                PRIMARY KEY (`ticket_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}
