<?php
namespace application\laika;

use common\libraries\Utilities;
use common\libraries\WebApplication;
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
		'laika_data_manager' => 'laika_data_manager.class.php',
		'laika_rights' => 'laika_rights.class.php',
		'laika_utilities' => 'laika_utilities.class.php',
		'laika_calculated_result' => 'laika_calculated_result.class.php',
		'laika_attempt' => 'laika_attempt.class.php',
		'laika_answer' => 'laika_answer.class.php',
		'laika_scale' => 'laika_scale.class.php',
		'laika_result' => 'laika_result.class.php',
		'laika_manager' => 'laika_manager/laika_manager.class.php'
		);  
		     
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('laika') . $url;
            return true;
        }
        
        return false;
	}
}

?>