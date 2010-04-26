<?php

/**
 * $Id: phpmailer_mail.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.mail.phpmailer
 */
require_once dirname(__FILE__) . '/../mail.class.php';
require_once Path :: get_plugin_path() . 'phpmailer/class.phpmailer.php';
/**
 * This class implements the abstract Mail class and uses the phpmailer project
 * to  send the emails.
 */
class PhpmailerMail extends Mail
{

    function send()
    {
//        $headers = array();
//        foreach ($this->get_cc() as $index => $cc)
//        {
//            $headers[] = 'Cc: ' . $cc;
//        }
//        foreach ($this->get_bcc() as $index => $bcc)
//        {
//            $headers[] = 'Bcc: ' . $bcc;
//        }
//        if (! is_null($this->get_from()))
//        {
//            $headers[] = 'From: ' . $this->get_from();
//
//            if (! is_null($this->get_reply()))
//            {
//                $headers[] = 'Reply-To: ' . $this->get_reply();
//
//            }
//            else
//            {
//                $headers[] = 'Reply-To: ' . $this->get_from();
//            }
//        }
//        $headers = implode("\n", $headers);

        global $phpmailer_config;
        require_once (dirname(__FILE__) . '/phpmailer.conf.php');
        $mail = new PHPMailer();
        $mail->CharSet = 'utf-8';
        $mail->Mailer = $phpmailer_config['SMTP_MAILER'];
        $mail->Host = $phpmailer_config['SMTP_HOST'];
        $mail->Port = $phpmailer_config['SMTP_PORT'];
        if ($phpmailer_config['SMTP_AUTH'])
        {
            $mail->SMTPAuth = 1;
            $mail->Username = $phpmailer_config['SMTP_USER'];
            $mail->Password = $phpmailer_config['SMTP_PASS'];
        }

        $mail->Priority = 3; // 5=low, 1=high
        $mail->AddCustomHeader('Errors-To: ' . $phpmailer_config['SMTP_FROM_EMAIL']);

        if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $this->get_message()))
        {
            $mail->IsHTML(1);
        }
        else
        {
            $mail->IsHTML(0);
        }
        $mail->SMTPKeepAlive = true;

        // attachments
        // $mail->AddAttachment($path);
        // $mail->AddAttachment($path,$filename);


        if (! is_null($this->get_from()))
        {
            $mail->From = $this->get_from_email();
            $mail->Sender = $this->get_from_email();
            $mail->FromName = $this->get_from_name();
            $mail->AddCustomHeader('Return-Path: '.$this->get_from_email());

            //$mail->ConfirmReadingTo = $this->get_from(); //Disposition-Notification
        }
         if(! is_null($this->get_reply())){

         	$mail->AddReplyTo($this->get_reply_email(), $this->get_reply_name());
         	$mail->AddCustomHeader('Return-Path: <'.$this->get_reply_email().'>');
         }


        else
        {
            $mail->From = $phpmailer_config['SMTP_FROM_EMAIL'];
            $mail->Sender = $phpmailer_config['SMTP_FROM_EMAIL'];
            $mail->FromName = $phpmailer_config['SMTP_FROM_NAME'];
            //$mail->ConfirmReadingTo = $phpmailer_config['SMTP_FROM_EMAIL']; //Disposition-Notification
        }

        $mail->Subject = $this->get_subject();
        $mail->Body = $this->get_message();
        foreach ($this->get_to() as $index => $recipient)
        {
            $mail->AddAddress($recipient, $recipient);
        }

        if (! $mail->Send())
        {
            return false;
        }
        $mail->ClearAddresses();
        return true;
    }
}
?>