<?php
namespace repository;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Filesystem;

/**
 * $Id: repository_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package repository
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
        $list = array('catalog',
                'complex_content_object_item_form',
                'complex_content_object_item',
                'complex_content_object_menu',
                'content_object_category_menu',
                'content_object_copier',
                'content_object_difference_display',
                'content_object_difference',
                'content_object_display',
                'content_object_form',
                'content_object_import_form',
                'content_object_include_parser',
                'content_object_installer',
                'content_object_pub_feedback',
                'content_object_publication_attributes',
                'content_object',
                'content_object_attachment',
                'content_object_updater',
                'difference_engine',
        		'external_instance',
        		'external_setting',
        		'external_user_setting',
                'external_sync_info',
                'external_sync',
                'quota_manager',
                'repository_data_class',
                'repository_data_manager',
                'repository_rights',
                'user_view_rel_content_object',
                'user_view',
                'content_object_renderer',
                'content_object_share',
                'content_object_user_share',
                'content_object_group_share',
                'content_object_type_selector',
                'content_object_type_selector_support',);

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
        $list = array(
                'repository_filter_form',
                'user_view_form',
                'content_object_share_form');

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
                'repository_browser_table' => 'browser/repository_browser_table.class.php',
                'repository_browser_gallery_table' => 'gallery_browser/repository_browser_gallery_table.class.php',
                'repository_version_browser_table' => 'version_browser/repository_version_browser_table.class.php',
                'template_browser_table' => 'browser/template_browser/template_browser_table.class.php',
                'repository_shared_content_objects_browser_table' => 'browser/shared_content_objects_browser/repository_shared_content_objects_browser_table.class.php',
                'complex_browser_table' => 'complex_browser/complex_browser_table.class.php',
                'publication_browser_table' => 'publication_browser/publication_browser_table.class.php',
                'recycle_bin_browser_table' => 'recycle_bin_browser/recycle_bin_browser_table.class.php',
                'user_view_browser_table' => 'user_view_browser/user_view_browser_table.class.php',
                'link_browser_table' => 'link_browser/link_browser_table.class.php',
                'external_link_browser_table' => 'external_link_browser/external_link_browser_table.class.php',
                'content_object_registration_browser_table' => 'content_object_registration_browser/content_object_registration_browser_table.class.php');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/lib/repository_manager/component/' . $url;
            return true;
        }

        return false;
    }

    static function check_for_special_files()
    {
        $list = array(
                'complex_builder' => 'complex_builder/complex_builder.class.php',
                'complex_builder_component' => 'complex_builder/complex_builder_component.class.php',
                'complex_display' => 'complex_display/complex_display.class.php',
                'complex_display_component' => 'complex_display/complex_display_component.class.php',
                'complex_display_support' => 'complex_display/complex_display_support.class.php',
                'complex_display_preview' => 'complex_display/complex_display_preview.class.php',
                'repository_category_manager' => 'category_manager/repository_category_manager.class.php',
                'repository_category' => 'category_manager/repository_category.class.php',
                'content_object_export' => 'export/content_object_export.class.php',
                'content_object_import' => 'import/content_object_import.class.php',
                'repository_manager' => 'repository_manager/repository_manager.class.php',
                'repository_manager_component' => 'repository_manager/repository_manager_component.class.php',
                'repository_search_form' => 'repository_manager/repository_search_form.class.php',
                'publisher_wizard' => 'repository_manager/component/publisher_wizard/publisher_wizard.class.php',
                'default_content_object_table_column_model' => 'content_object_table/default_content_object_table_column_model.class.php',
                'default_content_object_table_cell_renderer' => 'content_object_table/default_content_object_table_cell_renderer.class.php',
                'repository_block' => '../blocks/repository_block.class.php',
                'database_repository_data_manager' => 'data_manager/database_repository_data_manager.class.php',
                'external_instance_manager' => 'external_instance_manager/external_instance_manager.class.php',
                'external_repository_user_quotum' => 'external_repository_user_quotum.class.php');

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