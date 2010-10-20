<?php
/**
 * $Id: phpmailer.conf.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.mail.phpmailer
 */
/**
 * Change these values if you want to use phpmailer to send emails
 */
$phpmailer_config['SMTP_FROM_EMAIL'] = $administrator["email"];
$phpmailer_config['SMTP_FROM_NAME'] = $administrator["name"];
$phpmailer_config['SMTP_HOST'] = '';
$phpmailer_config['SMTP_PORT'] = 25;
$phpmailer_config['SMTP_MAILER'] = 'smtp'; //mail, sendmail or smtp
$phpmailer_config['SMTP_AUTH'] = 0;
$phpmailer_config['SMTP_USER'] = '';
$phpmailer_config['SMTP_PASS'] = '';

?>