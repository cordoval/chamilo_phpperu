<?php
namespace home;
use common\libraries\Utilities;
/**
 * $Id: home_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package home
 */

class HomeAutoloader
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
        $list = array('home_block_config', 'home_block', 'home_column', 'home_data_manager', 'home_row', 'home_tab');

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
        $list = array('home_block_config_form', 'home_block_form', 'home_column_form', 'home_row_form', 'home_tab_form');

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
                'home_manager' => 'home_manager/home_manager.class.php', 'home_manager_component' => 'home_manager/home_manager_component.class.php', 'build_wizard' => 'home_manager/component/wizards/build_wizard.class.php',
                'home_renderer' => 'home_renderer/home_renderer.class.php');

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