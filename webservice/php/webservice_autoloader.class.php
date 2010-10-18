<?php
namespace webservice;

use common\libraries\Utilities;
/**
 * $Id: webservice_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package webservice
 */

class WebserviceAutoloader
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

        if (self :: check_for_tables())
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
        $list = array('webservice_category_menu', 'webservice_category', 'webservice_credential', 'webservice_data_manager', 'webservice_registration', 'webservice_rights');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (in_array($lower_case, $list))
        {
            require_once dirname(__FILE__) . '/lib/' . $lower_case . '.class.php';
            return true;
        }

        return false;
    }

    static function check_for_tables()
    {
        $list = array('webservice_browser_table' => 'webservice_browser_table/webservice_browser_table.class.php');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/lib/webservice_manager/component/' . $url;
            return true;
        }

        return false;
    }

    static function check_for_special_files()
    {
        $list = array('webservice_manager' => 'webservice_manager/webservice_manager.class.php', 'webservice_manager_component' => 'webservice_manager/webservice_manager_component.class.php');

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