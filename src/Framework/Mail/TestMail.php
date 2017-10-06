<?php

namespace Framework\Mail;

/**
 * Class TestMail - Return mails to logs for test purpose
 * @package Framework\Mail
 */
class TestMail implements MailInterface
{

    /**
     * @var string
     */
    private $host;
    /**
     * @var int
     */
    private $port;
    /**
     * @var bool|null
     */
    private $isDebug;
    /**
     * @var bool|null
     */
    private $isSMTP;
    /**
     * @var bool|null
     */
    private $isSMTPAuth;
    /**
     * @var null|string
     */
    private $username;
    /**
     * @var null|string
     */
    private $password;
    /**
     * @var null|string
     */
    private $SMTPSecure;

    /**
     * @var [string, ? string]
     */
    private $from;

    /**
     * @var [string, string]
     */
    private $recipients;

    /**
     * @var [string, string]
     */
    private $replyTo;

    /**
     * @var string[]
     */
    private $CCC;

    /**
     * @var string[]
     */
    private $BCC;

    /**
     * @var [string, ? string]
     */
    private $attachments;

    /**
     * @var bool
     */
    private $isHTML;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $altBody;



    public function __construct()
    {
        $this->config();
        $this->path = dirname(__DIR__, 3) . "/Logs/Mail/";
    }

    /**
     * Set the server configuration
     *
     * @param string $host
     * @param int $port
     * @param bool|null $isDebug
     * @param bool|null $isSMTP
     * @param bool|null $isSMTPAuth
     * @param null|string $username
     * @param null|string $password
     * @param null|string $SMTPSecure
     */
    public function config(string $host = "localhost", int $port = 25, ? bool $isDebug = false, ? bool $isSMTP = true, ? bool $isSMTPAuth = true, ? string $username = "", ? string $password = "", ? string $SMTPSecure = "tls"): void
    {
        $this->host = $host;
        $this->port = $port;
        $this->isDebug = $isDebug;
        $this->isSMTP = $isSMTP;
        $this->isSMTPAuth = $isSMTPAuth;
        $this->username = $username;
        $this->password = $password;
        $this->SMTPSecure = $SMTPSecure;
    }

    /**
     * Specify From
     *
     * @param string $email
     * @param null|string $name
     * @return MailInterface
     */
    public function setFrom(string $email, ? string $name): MailInterface
    {
        $this->from = [$email, $name];
        return $this;
    }

    /**
     * Add adresse to recipient list
     *
     * @param string $email
     * @param null|string $name
     * @return MailInterface
     */
    public function addAddress(string $email, ? string $name): MailInterface
    {
        $this->recipients[] = [
            $email,
            $name
        ];
        return $this;
    }

    /**
     * Add adresse to reply to list
     *
     * @param string $email
     * @param null|string $name
     * @return MailInterface
     */
    public function addReplyTo(string $email, ? string $name): MailInterface
    {
        $this->replyTo[] = [
            $email,
            $name
        ];
        return $this;
    }

    /**
     * Add CCC
     *
     * @param string $email
     * @return MailInterface
     */
    public function addCCC(string $email): MailInterface
    {
        $this->CCC[] = [
            $email
        ];
        return $this;
    }

    /**
     * Add BCC
     *
     * @param string $email
     * @return MailInterface
     */
    public function addBCC(string $email): MailInterface
    {
        $this->BCC[] = [
            $email
        ];
        return $this;
    }

    /**
     * Add attachment from a path
     *
     * @param string $path
     * @param null|string $name
     * @return MailInterface
     */
    public function addAttachment(string $path, ? string $name = null): MailInterface
    {
        $this->attachments[] = [
            $path,
            $name
        ];
        return $this;
    }

    /**
     * @param bool $isHTML
     * @return MailInterface
     */
    public function isHTML(bool $isHTML): MailInterface
    {
        $this->isHTML = $isHTML;
        return $this;
    }

    /**
     * @param string $subject
     * @return MailInterface
     */
    public function subject(string $subject): MailInterface
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param string $body
     * @return MailInterface
     */
    public function body(string $body): MailInterface
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param string $altBody
     * @return MailInterface
     */
    public function altBody(string $altBody): MailInterface
    {
        $this->altBody = $altBody;
        return $this;
    }

    public function send()
    {
        $path = $this->path;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        file_put_contents($path . time(), $this->__toString());
        return $this;
    }

    public function __toString()
    {
        $string = "";

        $string .= "------------Config----------------\n";
        $string .= "host : " . $this->host . "\n";
        $string .= "port : " . $this->port . "\n";
        $string .= "debug : " . $this->isDebug . "\n";
        $string .= "SMTP : " . $this->isSMTP . "\n";
        $string .= "SMTP Auth : " . $this->isSMTPAuth . "\n";
        $string .= "username : " . $this->username . "\n";
        $string .= "password : " . $this->password . "\n";
        $string .= "SMTP secure : " . $this->SMTPSecure . "\n\n";

        $string .= "------------Mail----------------\n";

        $string .= "from : " . $this->from[0] . " " . $this->from[1] . " \n";


        $string .= "recipients : \n";

        foreach ($this->recipients as $recipient) {
            $string .= $recipient[0] . " " . $recipient[1] . " \n";
        }

        $string .= "reply to : " . $this->replyTo[0] . " " . $this->replyTo[1] . " \n";


        $string .= "subject : " . $this->subject . "\n\n";
        if ($this->isHTML) {
            $string .= "body (HTML) : \n" . $this->body . "\n\n";
        } else {
            $string .= "body : \n" . $this->body . "\n\n";
        }
        $string .= "altBody : " . $this->altBody . "\n\n";

        return $string;
    }
}
