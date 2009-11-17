<?php
/**
 * $Id: laika_mailer_form.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.forms
 */
class LaikaMailerForm extends FormValidator
{
    private $manager;
    
    private $user;

    function __construct($manager, $user, $url)
    {
        parent :: __construct('laika_mailer_form', 'post', $url);
        
        $this->manager = $manager;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    private function build_form()
    {
        $this->add_textfield('subject', Translation :: get('Subject'), true, array('size' => '100'));
        $this->add_html_editor('message', Translation :: get('Message'), true);
        
        $url = Path :: get(WEB_PATH) . 'user/xml_feeds/xml_user_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $hidden = false;
        
        $recipients = $this->manager->get_selected_users();
        
        $elem = $this->addElement('element_finder', 'recipients', Translation :: get('Recipients'), $url, $locale, $recipients);
        //$elem->excludeElements(array($this->user->get_id()));
        $elem->setDefaultCollapsed(false);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Send'), array('class' => 'positive send'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function setDefaults($defaults = array ())
    {
        parent :: setDefaults($defaults);
    }

    function send_mails()
    {
        $udm = UserDataManager :: get_instance();
        $values = $this->exportValues();
        
        $subject = $values['subject'];
        $message = $values['message'];
        $recipients = $values['recipients'];
        
        $user = $this->user;
        $from = array(Mail :: FROM_NAME => $user->get_fullname(), Mail :: FROM_EMAIL => $user->get_email());
        
        foreach ($recipients as $recipient)
        {
            $user_object = $udm->retrieve_user($recipient);
            
            $mail = Mail :: factory($subject, $message, $user_object->get_email(), $from);
            if (! $mail->send())
            {
                return false;
            }
        }
        
        return true;
    }
}
?>