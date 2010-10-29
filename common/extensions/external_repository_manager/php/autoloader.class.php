<?php

namespace common\extensions\external_repository_manager;

use common\libraries\Utilities;
use common\libraries\Path;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array(
            'external_repository_object' => 'external_repository_object.class.php', 'external_repository_object_display' => 'external_repository_object_display.class.php',
            'external_repository_component' => 'external_repository_component.class.php', 'external_repository_connector' => 'external_repository_connector.class.php',
            'external_repository_menu' => 'external_repository_menu.class.php', 'external_repository_object_renderer' => 'external_repository_object_renderer.class.php',
            'default_external_repository_object_table_data_provider' => 'table/default_external_repository_object_table_data_provider.class.php',
            'default_external_repository_object_table_column_model' => 'table/default_external_repository_object_table_column_model.class.php',
            'default_external_repository_object_table_cell_renderer' => 'table/default_external_repository_object_table_cell_renderer.class.php',
            'default_external_repository_gallery_object_table_property_model' => 'table/default_external_repository_gallery_object_table_property_model.class.php',
            'default_external_repository_gallery_object_table_data_provider' => 'table/default_external_repository_gallery_object_table_data_provider.class.php',
            'default_external_repository_gallery_object_table_cell_renderer' => 'table/default_external_repository_gallery_object_table_cell_renderer.class.php',
            'external_repository_browser_gallery_property_model' => 'component/external_repository_browser_gallery_table/external_repository_browser_gallery_table_property_model.class.php',
            'external_repository_manager' => 'external_repository_manager.class.php');

        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once Path :: get_common_extensions_path() . 'external_repository_manager/php/' . $url;
            return true;
        }

        return false;
    }

}

?>