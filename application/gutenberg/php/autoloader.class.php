<?php
namespace application\gutenberg;

use common\libraries\WebApplication;
use common\libraries\Utilities;
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class Autoloader
{
	static function load($classname)
	{
		$list = array(
		'gutenberg_publication' => 'gutenberg_publication.class.php',
		'gutenberg_data_manager' => 'gutenberg_data_manager.class.php',
		'gutenberg_data_manager_interface' => 'gutenberg_data_manager_interface.class.php',
		'gutenberg_publication_renderer' => 'gutenberg_publication_renderer.class.php',
		'gutenberg_manager' => 'gutenberg_manager/gutenberg_manager.class.php',
		'gutenberg_publication_renderer' => 'gutenberg_publication_renderer.class.php',
		'gutenberg_publication_form' => 'forms/gutenberg_publication_form.class.php'
		);  
		     
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('gutenberg') . $url;
            return true;
        }
        
        return false;
	}
}

?>