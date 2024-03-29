<?php
namespace common\libraries;
/**
 * $Id: default_mail.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.mail.default
 */
require_once dirname(__FILE__) . '/../mail.class.php';
/**
 * This class implements the abstract Mail class and uses the php mail()
 * function to send the emails.
 */
class DefaultMail extends Mail
{

    function send()
    {
        $headers = array();
        foreach ($this->get_cc() as $cc)
        {
            $headers[] = 'Cc: ' . $cc;
        }
        foreach ($this->get_bcc() as $bcc)
        {
            $headers[] = 'Bcc: ' . $bcc;
        }
        if (! is_null($this->get_from()))
        {
            $headers[] = 'From: ' . $this->get_from();
            $headers[] = 'Reply-To: ' . $this->get_from();
        }
        
        if (is_null($this->get_from()))
        {
            if (PlatformSetting :: get('no_reply_email'))
                $headers[] = 'From: ' . PlatformSetting :: get('no_reply_email');
            else
                $headers[] = 'From: ' . PlatformSetting :: get('administrator_email');
        }
        
        $headers = implode("\n", $headers);
        return mail(implode(',', $this->get_to()), $this->get_subject(), $this->get_message(), $headers);
    }
}
?>