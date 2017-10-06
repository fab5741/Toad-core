<?php

namespace Framework\Mail;

/**
 * Interface MailInterface
 */
interface MailInterface
{

    public function __construct();
    /**
     * Settings
     */

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
    public function config(string $host = "localhost", int $port = 25, ? bool $isDebug = false, ? bool $isSMTP = true, ? bool $isSMTPAuth, ? string $username = "", ? string $password = "", ? string $SMTPSecure): void;

    /**
     * Recipients
     */

    /**
     * Specify From
     *
     * @param string $email
     * @param null|string $name
     * @return MailInterface
     */
    public function setFrom(string $email, ? string $name): self;

    /**
     * Add adresse to recipient list
     *
     * @param string $email
     * @param null|string $name
     * @return MailInterface
     */
    public function addAddress(string $email, ? string $name): self;

    /**
     * Add adresse to reply to list
     *
     * @param string $email
     * @param null|string $name
     * @return MailInterface
     */
    public function addReplyTo(string $email, ? string $name): self;

    /**
     * Add CCC
     *
     * @param string $email
     * @return MailInterface
     */
    public function addCCC(string $email): self;

    /**
     * Add BCC
     *
     * @param string $email
     * @return MailInterface
     */
    public function addBCC(string $email): self;

    /**
     * Attachments
     */

    /**
     * Add attachment from a path
     *
     * @param string $path
     * @param null|string $name
     * @return MailInterface
     */
    public function addAttachment(string $path, ? string $name = ""): self;

    /**
     * Content
     *
     */

    /**
     * Set is mail should return html, or not
     *
     * @param bool $isHTML
     * @return MailInterface
     */
    public function isHTML(bool $isHTML): self;

    /**
     * Set is mail should return html, or not
     *
     * @param string $subject
     * @return MailInterface
     */
    public function subject(string $subject): self;

    /**
     * Set is mail should return html, or not
     *
     * @param string $body
     * @return MailInterface
     */
    public function body(string $body): self;

    /**
     * Set is mail should return html, or not
     * @param string $altBody
     * @return MailInterface
     */
    public function altBody(string $altBody): self;

    /**
     * Finally send your email
     * @return mixed
     */
    public function send();
}
