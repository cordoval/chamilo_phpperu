<?php

namespace application\gradebook;

use common\libraries\Utilities;
use common\libraries\WebApplication;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class GradebookAutoloader
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
            'gradebook_rights' => 'gradebook_rights.class.php',
            'gradebook_utilities' => 'gradebook_utilities.class.php',
            'gradebook_data_manager' => 'gradebook_data_manager.class.php',
            'gradebook_data_manager_interface' => 'gradebook_data_manager_interface.class.php',
            'evaluation' => 'evaluation.class.php',
            'grade_evaluation' => 'grade_evaluation.class.php',
            'internal_item' => 'internal_item.class.php',
            'external_item' => 'external_item.class.php',
            'format' => 'format.class.php',
            'gradebook_connector' => 'connector/gradebook_connector.class.php',
            'evaluation_manager' => 'evaluation_manager/evaluation_manager.class.php',
            'evaluation_manager_interface' => 'evaluation_manager/evaluation_manager_interface.class.php',
            'gradebook_connector' => 'connector/gradebook_connector.class.php',
            'gradebook_manager' => 'gradebook_manager/gradebook_manager.class.php',
            'evaluation_form' => 'forms/evaluation_form.class.php',
            'evaluation_format' => 'evaluation_format/evaluation_format.class.php',
            'gradebook_tree_menu_data_provider' => 'data_provider/gradebook_tree_menu_data_provider.class.php'
        );
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('gradebook') . $url;
            return true;
        }

        return false;
    }

}

?>