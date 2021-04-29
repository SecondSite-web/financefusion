<?php
/**
 * File:          Cf.php
 * File Created:  2021/04/03 17:27
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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Cf
{
    /**
     * Sets the contact form table name
     * @var string
     */
    private string $contactTable = 'dash_contact_form';

    /**
     * SQL PDO Object
     * @var PDO
     */
    private PDO $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        if (!$this->pdo->query("SHOW TABLES LIKE '{$this->contactTable}'")->fetchAll()) {
            $this->createTable();
        }
        date_default_timezone_set('Africa/Johannesburg');
    }

    /**
     * Fetch all contact form submissions in an array
     * @return array
     */
    public function getAll(): array
    {
        return $this->pdo->query(
            "SELECT * FROM {$this->contactTable}  ORDER BY id ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function openCount()
    {
        $table = $this->contactTable;
        $sql = "SELECT count(*) FROM {$table} WHERE NOT status = ?";
        $result = $this->pdo->prepare($sql);
        $result->execute(['closed']);
        return $result->fetchColumn();
    }

    /**
     * Get an array of the column names
     * @return array
     */
    public function getColumnNames(): array
    {
        return $this->pdo->query("DESCRIBE {$this->contactTable}")->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Sends the contact form email
     *
     * @param $fields
     * @return bool|string
     */
    public function cfMailer($fields): bool|string
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
            $mail->addAddress(SMTP_EMAIL, SMTP_NAME);              // Add a recipient
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

            $message = file_get_contents(ROOT_PATH.'./email-templates/contact-form.html');
            $message = str_replace(
                array(
                '%firstname%', '%lastname%', '%email%', '%telephone%', '%message%'
                ),
                array(
                $fields['firstname'],
                $fields['lastname'],
                $fields['email'],
                $fields['telephone'],
                $fields['message']),
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

    public function setStatus($status, $id): bool
    {
        $table = $this->contactTable;
        $sql = "UPDATE {$table} SET status=? WHERE id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    /**
     * Saves Contact form submission to the database.
     *
     * @param mixed $fields
     *
     * @return bool [type]
     */
    public function cfDb(mixed $fields): bool
    {
        $table = $this->contactTable;

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$table} (firstname, lastname, telephone, 
            email, message, status)
    VALUES (:firstname, :lastname, :telephone, :email, :message, :status)"
            );
            $stmt->bindParam(':firstname', $fields['firstname']);
            $stmt->bindParam(':lastname', $fields['lastname']);
            $stmt->bindParam(':telephone', $fields['telephone']);
            $stmt->bindParam(':email', $fields['email']);
            $stmt->bindParam(':message', $fields['message']);
            $stmt->bindParam(':status', $fields['status']);
            $stmt->execute();
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    /**
     * Creates the Contact Form Table
     *
     */
    public function createTable(): void
    {
        $table = $this->contactTable;
        try {
            $sql ="CREATE TABLE IF NOT EXISTS $table (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `firstname` varchar(255) NOT NULL,
                `lastname` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `telephone` varchar(255) NULL,
                `message` text NOT NULL,
                `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` varchar(20) NOT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;" ;
            $this->pdo->exec($sql);
            // print("Created $table Table.\n");
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}
