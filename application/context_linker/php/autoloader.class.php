<?php

namespace application\context_linker;

use common\libraries\Utilities;
use common\libraries\WebApplication;

class Autoloader
{

    static function load($classname)
    {

        $list = array(
            'context_link' => 'context_link.class.php',
            'context_linker_data_manager' => 'context_linker_data_manager.class.php',
            'context_linker_data_manager_interface' => 'context_linker_data_manager_interface.class.php',
            'default_context_link_table_cell_renderer' => 'tables/context_link_table/default_context_link_table_cell_renderer.class.php',
            'default_context_link_table_column_model' => 'tables/context_link_table/default_context_link_table_column_model.class.php',
            'confirmation_form' => 'forms/confirmation_form.class.php',
            'context_link_form' => 'forms/context_link_form.class.php',
            'context_link_table' => 'context_linker_manager/component/tables/context_link_table/context_link_table.class.php',
            'context_link_table_cell_renderer' => 'context_linker_manager/component/tables/context_link_table/context_link_table/context_link_table_cell_renderer.class.php',
            'context_link_table_data_provider' => 'context_linker_manager/component/tables/context_link_table/context_link_table/context_link_table_data_provider.class.php',
            'context_link_table_column_model' => 'context_linker_manager/component/tables/context_link_table/context_link_table/context_link_table_column_model.class.php',
            'context_linker_manager' => 'context_linker_manager/context_linker_manager.class.php',
            'browser' => 'context_linker_manager/component/browser.class.php',
            'content_objects_browser' => 'context_linker_manager/component/.content_objects_browserclass.php',
            'content_link_creator' => 'context_linker_manager/component/content_link_creator.class.php',
            'content_link_updater' => 'context_linker_manager/component/content_link_updater.class.php',
            'content_link_deleter' => 'context_linker_manager/component/content_link_deleter.class.php',
            'content_link_publisher' => 'context_linker_manager/component/content_link_publisher.class.php',
            'content_links_browser' => 'context_linker_manager/component/content_links_browser.class.php'
        );

        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('context_linker') . $url;
            return true;
        }

        return false;
    }

}

?>
