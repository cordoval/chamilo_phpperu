<?php
namespace application\handbook;
use common\libraries\Utilities;
use common\libraries\WebApplication;

class Autoloader
{
    static function load($classname)
	{
	
        $list = array(
            'handbook_data_manager' => 'handbook_data_manager.class.php',
            'handbook_preference' => 'handbook_preference.class.php',
            'handbook_rights' => 'handbook_rights.class.php',
            'handbook_publication' => 'handbook_publication.class.php',
            'handbook_manager' => 'handbook_manager/handbook_manager.class.php'
            );


        $lower_case = Utilities :: camelcase_to_underscores($classname);
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('handbook') . $url;
            return true;
        }

        return false;
	}


}
