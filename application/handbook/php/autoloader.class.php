<?php
namespace application\handbook;
use common\libraries\Utilities;

class Autoloader
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
           'handbook_data_manager' => 'handbook_data_manager.class.php',
            'handbook_preference' => 'handbook_preference.class.php',
            'handbook_rights' => 'handbook_rights.class.php',
            'handbook_publication' => 'handbook_publication.class.php',
            'handbook_manager' => '/handbook_manager/handbook_manager.class.php'
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

?>