<?php
/**
 * $Id: email_form.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.category_manager
 */

class EmailForm extends FormValidator
{
	private $user;
	private $target_users;
	
    /**
     * Creates a new LanguageForm
     */
    function EmailForm($action, $user, $target_users)
    {
        parent :: __construct('email_form', 'post', $action);
        
        $this->target_users = $target_users;
        $this->user = $user;
        
        $this->build_form();
    }

    function build_form()
    {
		$this->addElement('category', Translation :: get('Email'));
		
    	$this->addElement('text', 'title', Translation :: get('Title'), array('size' => '50'));
		$this->addRule('title', Translation :: get('ThisFieldIsRequired'), 'required');        
		
		$this->add_html_editor('message', Translation :: get('Message'), true, array('height' => 500, 'width' => 750));
		
		$this->addElement('category');
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Email'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function email()
    {
        $values = $this->exportValues();
        
        $title = $values['title'];
        $message = $values['message'];
        $targets = $this->get_target_email_addresses();
        
        $mail = Mail :: factory($title, $message, $targets, array($this->user->get_email()));
        $mail->send();
        
        return true;
    }
    
    function get_target_email_addresses()
    {
    	$email_addresses = array();
    	
    	foreach($this->target_users as $target_user)
    	{
    		if(is_object($target_user) && get_class($target_user) == 'User')
    		{
    			$email_addresses[] = $target_user->get_email();
    		}
    		else
    		{
    			$email_addresses[] = $target_user;
    		}
    	}
    	
    	return $email_addresses;
    }
}
?>