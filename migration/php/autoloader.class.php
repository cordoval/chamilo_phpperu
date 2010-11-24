<?php

namespace migration;

use common\libraries\Utilities;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package migration
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

        if (self :: check_for_special_files())
        {
            return true;
        }

        return false;
    }

    static function check_for_general_files()
    {
        $list = array(
            'migration_data_manager', 'old_migration_data_manager', 'migration_data_class', 'migration_data_manager_interface', 'failed_element', 'file_recovery', 'id_reference', 'migration_block', 'migration',
            'migration_block_registration', 'migration_properties', 'migration_database', 'migration_database_connection', 'platform_migration_data_manager');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (in_array($lower_case, $list))
        {
            require_once dirname(__FILE__) . '/lib/' . $lower_case . '.class.php';
            return true;
        }

        return false;
    }

    static function check_for_special_files()
    {
        $list = array('migration_manager' => 'migration_manager/migration_manager.class.php', 'migration_form' => 'forms/migration_form.class.php');

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