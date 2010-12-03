<?php

namespace common\extensions\video_conferencing_manager;

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
                'video_conferencing_object' => 'video_conferencing_object.class.php', 'video_conferencing_object_display' => 'video_conferencing_object_display.class.php', 
                'video_conferencing_component' => 'video_conferencing_component.class.php', 'video_conferencing_manager_connector' => 'video_conferencing_manager_connector.class.php', 
                'video_conferencing_menu' => 'video_conferencing_menu.class.php', 'video_conferencing_object_renderer' => 'video_conferencing_object_renderer.class.php', 
                'video_conferencing_rights' => 'video_conferencing_rights.class.php', 'default_video_conferencing_object_table_data_provider' => 'table/default_video_conferencing_object_table_data_provider.class.php', 
                'default_video_conferencing_object_table_column_model' => 'table/default_video_conferencing_object_table_column_model.class.php', 
                'default_video_conferencing_object_table_cell_renderer' => 'table/default_video_conferencing_object_table_cell_renderer.class.php', 
                'default_video_conferencing_gallery_object_table_property_model' => 'table/default_video_conferencing_gallery_object_table_property_model.class.php', 
                'default_video_conferencing_gallery_object_table_data_provider' => 'table/default_video_conferencing_gallery_object_table_data_provider.class.php', 
                'default_video_conferencing_gallery_object_table_cell_renderer' => 'table/default_video_conferencing_gallery_object_table_cell_renderer.class.php', 
                'video_conferencing_browser_gallery_property_model' => 'component/video_conferencing_browser_gallery_table/video_conferencing_browser_gallery_table_property_model.class.php', 
                'video_conferencing_manager' => 'video_conferencing_manager.class.php');
        
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once Path :: get_common_extensions_path() . 'video_conferencing_manager/php/' . $url;
            return true;
        }
        
        return false;
    }

}

?>