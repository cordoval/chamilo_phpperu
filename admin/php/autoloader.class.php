<?php

namespace admin;

use common\libraries\Utilities;

/**
 * $Id: admin_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package admin
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
        $list = array(
                'admin_block', 'admin_data_manager', 'admin_rights', 'configuration_form', 'feedback_publication', 'language_form', 'language', 'registration', 'remote_package', 'setting', 'system_announcement_publication_form',
                'system_announcement_publication', 'validation');

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
        $list = array('system_announcement_publication_browser_table' => 'system_announcement_publication_browser/system_announcement_publication_browser_table.class.php', 'whois_online_table' => 'whois_online_table/whois_online_table.class.php');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/lib/admin_manager/component/' . $url;
            return true;
        }

        return false;
    }

    static function check_for_special_files()
    {
        $list = array(
            'admin_manager' => 'admin_manager/admin_manager.class.php',
            'admin_manager_component' => 'admin_manager/admin_manager_component.class.php',
            'admin_search_form' => 'admin_manager/admin_search_form.class.php',
            'system_announcer_multipublisher' => 'announcer/system_announcement_multipublisher.class.php',
            'admin_category_manager' => 'category_manager/admin_category_manager.class.php',
            'package_installer' => 'package_installer/package_installer.class.php',
            'package_installer_source' => 'package_installer/package_installer_source.class.php',
            'package_updater' => 'package_updater/package_updater.class.php',
            'package_manager' => 'package_manager/package_manager.class.php',
            'package_remover' => 'package_remover/package_remover.class.php',
            'package_info' => 'package_installer/source/package_info/package_info.class.php',
            'content_object_registration_browser_table_data_provider' => 'package_manager/component/registration_browser/content_object/content_object_registration_browser_table_data_provider.class.php',
            'content_object_registration_browser_table_column_model' => 'package_manager/component/registration_browser/content_object/content_object_registration_browser_table_column_model.class.php',
            'content_object_registration_browser_table_cell_renderer' => 'package_manager/component/registration_browser/content_object/content_object_registration_browser_table_cell_renderer.class.php',
            'content_object_registration_browser_table' => 'package_manager/component/registration_browser/content_object/content_object_registration_browser_table.class.php',
            'package_dependency' => 'package_manager/package_dependency.class.php',
        );

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