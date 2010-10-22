<?php
namespace application\cas_user;

use common\libraries\Utilities;
use common\libraries\WebApplication;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class CasUserAutoloader
{
	static function load($classname)
	{
		$classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
            return false;
        }
        else
        {
            $classname = $classname_parts[count($classname_parts) - 1];
            array_pop($classname_parts);
            if (implode('\\', $classname_parts) != __NAMESPACE__)
            {
                return false;
            }
        }
	    
		$list = array(
    		'cas_user_data_manager' => 'cas_user_data_manager.class.php',
    		'cas_user_data_manager_interface' => 'cas_user_data_manager_interface.class.php',
    		'cas_user_manager' => 'cas_user_manager/cas_user_manager.class.php'
		);  
		     
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('cas_user') . $url;
            return true;
        }
        
        return false;
	}
}

?>