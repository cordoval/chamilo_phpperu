<?php

namespace common\extensions\repo_viewer;

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
            'repo_viewer' => 'repo_viewer.class.php',
            'repo_viewer_interface' => 'repo_viewer_interface.class.php',
            'repo_viewer_browser_component' => 'component/browser.class.php',
            'repo_viewer_viewer_component' => 'component/viewer.class.php',
            'repo_viewer_creator_component' => 'component/creator.class.php',
            'content_object_table_data_provider' => 'component/content_object_table/content_object_table_data_provider.class.php',
            'content_object_table_column_model' => 'component/content_object_table/content_object_table_column_model.class.php',
            'content_object_table_cell_renderer' => 'component/content_object_table/content_object_table_cell_renderer.class.php',
            'content_object_table' => 'component/content_object_table/content_object_table.class.php',
        );
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once Path :: get_common_extensions_path() . 'repo_viewer/php/' . $url;
            return true;
        }

        return false;
    }

}

?>