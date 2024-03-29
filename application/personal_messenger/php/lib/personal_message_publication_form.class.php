<?php

namespace application\personal_messenger;

use common\libraries\Path;
use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;
use group\GroupDataManager;
use common\libraries\EqualityCondition;
/**
 * $Id: personal_message_publication_form.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_plugin_path() . 'html2text/class.html2text.inc';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class PersonalMessagePublicationForm extends FormValidator
{
    /**#@+
     * Constant defining a form parameter
     */
    
    /**#@-*/
    /**
     * The learning object that will be published
     */
    private $content_object;
    /**
     * The publication that will be changed (when using this form to edit a
     * publication)
     */
    private $form_user;

    //private $publication;
    

    /**
     * Creates a new learning object publication form.
     * @param ContentObject The learning object that will be published
     * @param string $tool The tool in which the object will be published
     * @param boolean $email_option Add option in form to send the learning
     * object by email to the receivers
     */
    //function PersonalMessengerPublicationForm($content_object, $publication = null, $form_user, $action)
    function __construct($content_object, $form_user, $action)
    {
        parent :: __construct('publish', 'post', $action);
        $this->content_object = $content_object;
        //$this->publication = $publication;
        $this->form_user = $form_user;
        $this->build_form();
        $this->setDefaults();
    }

    /**
     * Sets the default values of the form.
     *
     * By default the publication is for everybody who has access to the tool
     * and the publication will be available forever.
     */
    function setDefaults()
    {
        $defaults = array();
        parent :: setDefaults($defaults);
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    function build_form()
    {
        //    	$publication = $this->publication;
        $recipients = array();
        //    	if ($publication)
        //    	{
        //			$publication = $this->publication;
        //			$recip = $publication->get_publication_sender();
        //			$recipient = array ();
        //			$recipient['id'] = $recip->get_id();
        //			$recipient['class'] = 'type type_user';
        //			$recipient['title'] = $recip->get_username();
        //			$recipient['description'] = $recip->get_lastname() . ' ' . $recip->get_firstname();
        //			$recipients[$recipient['id']] = $recipient;
        //    	}
        

        $url = Path :: get(WEB_PATH) . 'common/libraries/php/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching', null , Utilities :: COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null , Utilities :: COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null , Utilities :: COMMON_LIBRARIES);
        
        $elem = $this->addElement('user_group_finder', 'recipients', Translation :: get('Recipients'), $url, $locale, $recipients);
        $elem->excludeElements(array('user_' . $this->form_user->get_id()));
        $elem->setDefaultCollapsed(false);
        
        //$this->addElement('submit', 'submit', Translation :: get('Ok', null , Utilities :: COMMON_LIBRARIES));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Send'), array('class' => 'positive send'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null , Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    private $failures = 0;
    private $sent_users = array();

    /**
     * Creates a learning object publication using the values from the form.
     * @return ContentObjectPublication The new publication
     */
    function create_content_object_publication($extra_rec = array())
    {
        $values = $this->exportValues();
        $failures = 0;
        
        $recipients = $values['recipients'];
        
        if ($extra_rec && (count($extra_rec) > 0))
        { 
            if($recipients['user'])
            {
        		$selected_users = array_merge($extra_rec, $recipients['user']);
            }
            else
            {
            	$selected_users = $extra_rec;
            }
        }
        else
        {
            $selected_users = $recipients['user'];
        }

        foreach ($recipients['group'] as $group)
        {
            $grus = GroupDataManager :: get_instance()->retrieve_group_rel_users(new EqualityCondition('group_id', $group));
            while ($gru = $grus->next_result())
            {
                $selected_users[] = $gru->get_user_id();
            }
        
        }
        
        foreach ($selected_users as $user)
        {
            if ($user != $this->form_user->get_id())
            {
                if (! in_array($user, $this->sent_users))
                {
                    if(!$this->send_to_recipient($user))
                    {
                    	$failures ++;
                    }
                }
            }
        }
        
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    private function send_to_recipient($recip)
    {
        $sender_pub = new PersonalMessengerPublication();
        $sender_pub->set_personal_message($this->content_object->get_id());
        $sender_pub->set_recipient($recip);
        $sender_pub->set_published(time());
        $sender_pub->set_user($this->form_user->get_id());
        $sender_pub->set_sender($this->form_user->get_id());
        $sender_pub->set_status('0');
        
        if ($sender_pub->create())
        {
            $recipient_pub = new PersonalMessengerPublication();
            $recipient_pub->set_personal_message($this->content_object->get_id());
            $recipient_pub->set_recipient($recip);
            $recipient_pub->set_published(time());
            $recipient_pub->set_user($recip);
            $recipient_pub->set_sender($this->form_user->get_id());
            $recipient_pub->set_status('1');
            if ($recipient_pub->create())
            {
                $this->sent_users[] = $recip;
                return true;
            }
            else
            {
               return false;
            }
        }
        else
        {
            return false;
        }
    
    }
}
?>