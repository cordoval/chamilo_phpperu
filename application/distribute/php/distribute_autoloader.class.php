<?php
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class DistributeAutoloader
{
	static function load($classname)
	{
		$list = array(
		'distribute_data_manager' => 'distribute_data_manager.class.php',
		'announcement_distribution' => 'announcement_distribution.class.php',
		'distribute_data_manager_interface' => 'distribute_data_manager_interface.class.php',
		'distribute_manager' => 'distribute_manager/distribute_manager.class.php'
		);  
		     
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('distribute') . $url;
            return true;
        }
        
        return false;
	}
}

?>