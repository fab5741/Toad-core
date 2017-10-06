<?php

namespace Framework\Mail;

/**
 * Interface MailInterface
 */
class PhpMail implements MailInterface
{
    public function __construct()
    {
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
    public function config(string $host = "localhost", int $port = 25, ? bool $isDebug = false, ? bool $isSMTP = true, ? bool $isSMTPAuth, ? string $username = "", ? string $password = "", ? string $SMTPSecure): void
    {
        // TODO: Implement config() method.
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
        // TODO: Implement setFrom() method.
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
        // TODO: Implement addAddress() method.
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
        // TODO: Implement addReplyTo() method.
    }

    /**
     * Add CCC
     *
     * @param string $email
     * @return MailInterface
     */
    public function addCCC(string $email): MailInterface
    {
        // TODO: Implement addCCC() method.
    }

    /**
     * Add BCC
     *
     * @param string $email
     * @return MailInterface
     */
    public function addBCC(string $email): MailInterface
    {
        // TODO: Implement addBCC() method.
    }

    /**
     * Add attachment from a path
     *
     * @param string $path
     * @param null|string $name
     * @return MailInterface
     */
    public function addAttachment(string $path, ? string $name = ""): MailInterface
    {
        // TODO: Implement addAttachment() method.
    }

    /**
     * Set is mail should return html, or not
     *
     * @param bool $isHTML
     * @return MailInterface
     */
    public function isHTML(bool $isHTML): MailInterface
    {
        // TODO: Implement isHTML() method.
    }

    /**
     * Set is mail should return html, or not
     *
     * @param string $subject
     * @return MailInterface
     */
    public function subject(string $subject): MailInterface
    {
        // TODO: Implement subject() method.
    }

    /**
     * Set is mail should return html, or not
     *
     * @param string $body
     * @return MailInterface
     */
    public function body(string $body): MailInterface
    {
        // TODO: Implement body() method.
    }

    /**
     * Set is mail should return html, or not
     * @param string $altBody
     * @return MailInterface
     */
    public function altBody(string $altBody): MailInterface
    {
        // TODO: Implement altBody() method.
    }

    /**
     * Finally send your email
     * @return mixed
     */
    public function send()
    {
        // TODO: Implement send() method.
    }
}
