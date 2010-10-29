<?php
namespace group;
use common\libraries\Utilities;
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class Autoloader
{
    public static $class_name;

    static function load($classname)
    {
        self :: $class_name = $classname;
       

        if (self :: check_for_general_files())
        {
            return true;
        }

        if (self :: check_for_form_files())
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
        $list = array('group_data_manager', 'group_menu', 'group_rel_user', 'group_rights_template', 'group');

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
        $list = array('group_export_form', 'group_form', 'group_import_form', 'group_move_form', 'group_role_manager_form', 'group_search_form', 'group_user_search_form', 'group_user_import_form');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (in_array($lower_case, $list))
        {
            require_once dirname(__FILE__) . '/lib/forms/' . $lower_case . '.class.php';
            return true;
        }

        return false;
    }

    static function check_for_tables()
    {
        $list = array(
                'group_browser_table' => 'group_browser/group_browser_table.class.php', 'group_rel_user_browser_table' => 'group_rel_user_browser/group_rel_user_browser_table.class.php',
                'subscribe_user_browser_table' => 'subscribe_user_browser/subscribe_user_browser_table.class.php');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/lib/group_manager/component/' . $url;
            return true;
        }

        return false;
    }

    static function check_for_special_files()
    {
        $list = array('group_manager' => 'group_manager/group_manager.class.php', 'group_validator' => '../validator/group_validator.class.php', 'subscribe_wizard' => 'group_manager/component/wizards/subscribe_wizard.class.php');

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