<?php
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class WikiAutoloader
{
	static function load($classname)
	{
		$list = array(
		'wiki_publication' => 'wiki_publication.class.php',
		'wiki_pub_feedback' => 'wiki_pub_feedback.class.php',
		'wiki_data_manager_interface' => 'wiki_data_manager_interface.class.php',
		'wiki_data_manager' => 'wiki_data_manager.class.php',
		'wiki_publication_form' => 'forms/wiki_publication_form.class.php',
		'wiki_manager' => 'wiki_manager/wiki_manager.class.php'
		);  
		     
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('wiki') . $url;
            return true;
        }
        
        return false;
	}
}

?>