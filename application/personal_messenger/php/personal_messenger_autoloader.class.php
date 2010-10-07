<?php
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class PersonalMessengerAutoloader
{
	static function load($classname)
	{
		$list = array(
        'personal_messenger_manager' => 'personal_messenger_manager/personal_messenger_manager.class.php',
        'personal_message_publication' => 'personal_message_publication.class.php', 
        'personal_messenger_data_manager_interface' => 'personal_messenger_data_manager_interface.class.php',
        'personal_messenger_data_manager' => 'personal_messenger_data_manager.class.php', 
        'personal_messenger_menu' => 'personal_messenger_menu.class.php',
        'personal_message_publication' => 'personal_message_publication.class.php',
        'personal_message_publication_form' => 'personal_message_publication_form.class.php',
		'pm_publication_browser_table' => 'personal_messenger_manager/component/pm_publication_browser/pm_publication_browser_table.class.php);
        
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('personal_messenger') . $url;
            return true;
        }
        
        return false;
	}
}

?>