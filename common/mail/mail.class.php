<?php
/**
 * $Id: mail.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.mail
 */
/**
 * An abstract class for sending emails. Implement new mail methods by creating a
 * class which extends this abstract class.
 * @todo: Add functionality for extra headers, names of receivers & sender,
 * maybe HTML email and attachments?
 */
abstract class Mail
{
    const FROM_NAME = 'name';
    const FROM_EMAIL = 'email';
    
    /**
     * The sender of the mail
     * An array containing the name AND the e-mail address
     */
    private $from;
    /**
     * Array of receivers in the TO field of the mail
     */
    private $to;
    /**
     * Array of receivers in the CC field of the mail
     */
    private $cc;
    /**
     * Array of receivers in the BCC field of the mail
     */
    private $bcc;
    /**
     * The subject of the mail
     */
    private $subject;
    /**
     * The message of the mail
     */
    private $message;

    /**
     * Constructor
     */
    function Mail($subject, $message, $to, $from = null, $cc = array (), $bcc = array ())
    {
        $this->subject = $subject;
        $this->message = $message;
        if (! is_array($to))
        {
            $to = array($to);
        }
        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->from = $from;
    }

    /**
     * Create a new mail instance.
     * @todo This function now uses the DefaultMail-class. The class to use
     * should be configurable.
     */
    public static function factory($subject, $message, $to, $from = array(), $cc = array(), $bcc = array())
    {
        // TODO: This value should come from configuration and can be one of the available mail-implementations
        $mail_file = 'phpmailer';
        require_once dirname(__FILE__) . '/' . $mail_file . '/' . $mail_file . '_mail.class.php';
        $mail_class = Utilities :: underscores_to_camelcase($mail_file) . 'Mail';
        return new $mail_class($subject, $message, $to, $from, $cc, $bcc);
    }

    /**
     * Retrieves the subject for the email
     * @return string
     */
    function get_subject()
    {
        return $this->subject;
    }

    /**
     * Retrieves the message for the email
     * @return string
     */
    function get_message()
    {
        return $this->message;
    }

    /**
     * Retrieves the receiver(s) in the TO-field of the email
     * @return array
     */
    function get_to()
    {
        return $this->to;
    }

    /**
     * Retrieves the receiver(s) in the CC-field of the email
     * @return array
     */
    function get_cc()
    {
        return $this->cc;
    }

    /**
     * Retrieves the receiver(s) in the BCC-field of the email
     * @return array
     */
    function get_bcc()
    {
        return $this->bcc;
    }

    /**
     * Retrieves the sender of the email
     * @return array
     */
    function get_from()
    {
        return $this->from;
    }

    function get_from_name()
    {
        return $this->from[self :: FROM_NAME];
    }

    function get_from_email()
    {
        return $this->from[self :: FROM_EMAIL];
    }

    /**
     * Send the email
     * @return boolean True if the mail was successfully sent, false if not.
     */
    abstract function send();
}
?>