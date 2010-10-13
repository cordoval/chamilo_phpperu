<?php
namespace rights;

use common\libraries\Utilities;
/**
 * $Id: rights_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package rights
 */

class RightsAutoloader
{
    public static $class_name;

    static function load($classname)
    {
        $classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
            return false;
        }
        else
        {
            self :: $class_name = $classname_parts[count($classname_parts) - 1];
            array_pop($classname_parts);
            if (implode('\\', $classname_parts) != __NAMESPACE__)
            {
                return false;
            }
        }

        if (self :: check_for_general_files())
        {
            return true;
        }

        if (self :: check_for_form_files())
        {
            return true;
        }

        if (self :: check_for_special_files())
        {
            return true;
        }

        return false;
    }

    static function check_for_general_files()
    {
        $list = array(
                'group_right_location', 'location_menu', 'location_right_menu', 'location', 'right', 'rights_data_manager', 'rights_template_right_location', 'rights_template', 'rights_utilities', 'user_right_location', 'type_template',
                'type_template_right_location');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (in_array($lower_case, $list))
        {
            require_once dirname(__FILE__) . '/lib/' . $lower_case . '.class.php';
            return true;
        }

        return false;
    }

    static function check_for_form_files()
    {
        $list = array('right_request_form', 'rights_template_form', 'type_template_form');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (in_array($lower_case, $list))
        {
            require_once dirname(__FILE__) . '/lib/forms/' . $lower_case . '.class.php';
            return true;
        }

        return false;
    }

    static function check_for_special_files()
    {
        $list = array(
                'rights_manager' => 'rights_manager/rights_manager.class.php', 'rights_manager_component' => 'rights_manager/rights_manager_component.class.php',
                'group_right_manager' => 'group_right_manager/group_right_manager.class.php', 'group_right_manager_component' => 'group_right_manager/group_right_manager_component.class.php',
                'location_manager' => 'location_manager/location_manager.class.php', 'location_manager_component' => 'location_manager/location_manager_component.class.php',
                'type_template_manager' => 'type_template_manager/type_template_manager.class.php', 'rights_template_manager' => 'rights_template_manager/rights_template_manager.class.php',
                'rights_template_manager_component' => 'rights_template_manager/rights_template_manager_component.class.php', 'user_right_manager' => 'user_right_manager/user_right_manager.class.php',
                'user_right_manager_component' => 'user_right_manager/user_right_manager_component.class.php');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/lib/' . $url;
            return true;
        }

        return false;
    }
}

?>