<?php
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class PhrasesAutoloader
{
	static function load($classname)
	{
		$list = array(
		'phrases_data_manager' => 'phrases_data_manager.class.php',
		'phrases_publication' => 'phrases_publication.class.php',
		'phrases_data_manager' => 'phrases_data_manager.class.php',
		'phrases_data_manager_interface' => 'phrases_data_manager_interface.class.php',
		'phrases_mastery_level' => 'phrases_mastery_level.class.php',
		'phrases_publication_menu' => 'phrases_publication_menu.class.php',
		'phrases_publication_form' => 'forms/phrases_publication_form.class.php',
		'phrases_publication_manager' => 'phrases_manager/component/publication_manager/publication_manager.class.php',
		'phrases_manager' => 'phrases_manager/phrases_manager.class.php',
		'phrases_publisher' => 'publisher/phrases_publisher.class.php',
		);  
		     
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('phrases') . $url;
            return true;
        }
        
        return false;
	}
}

?>