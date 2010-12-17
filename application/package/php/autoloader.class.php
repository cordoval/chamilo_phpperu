<?php

namespace application\package;

use common\libraries\Utilities;
use common\libraries\WebApplication;
/**
 * $Id: user_autoloader 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class Autoloader
{

    static function load($classname)
    {
        $list = array('package_data_manager' => 'package_data_manager', 
                'package_data_manager_interface' => 'package_data_manager_interface', 
                'package_manager' => 'package_manager/package_manager', 
                'package_instance_manager' => 'package_instance_manager/package_instance_manager', 
                'author_manager' => 'author_manager/author_manager', 
                'dependency_manager' => 'dependency_manager/dependency_manager', 
                'package' => 'package', 
                'author' => 'author', 
                'package_form' => 'forms/package_form', 
                'author_form' => 'forms/author_form', 
                'package_browser_table' => 'package_instance_manager/component/browser/package_browser_table', 
                'package_browser_table_data_provider' => 'package_instance_manager/component/browser/package_browser_table_data_provider', 
                'package_browser_table_cell_renderer' => 'package_instance_manager/component/browser/package_browser_table_cell_renderer', 
                'package_browser_table_column_model' => 'package_instance_manager/component/browser/package_browser_table_column_model', 
                'default_package_table_column_model' => 'tables/package_table/default_package_table_column_model', 
                'default_package_table_cell_renderer' => 'tables/package_table/default_package_table_cell_renderer', 
                'author_browser_table' => 'author_manager/component/browser/author_browser_table', 
                'author_browser_table_data_provider' => 'author_manager/component/browser/author_browser_table_data_provider', 
                'author_browser_table_cell_renderer' => 'author_manager/component/browser/author_browser_table_cell_renderer', 
                'author_browser_table_column_model' => 'author_manager/component/browser/author_browser_table_column_model', 
                'default_author_table_column_model' => 'tables/author_table/default_author_table_column_model', 
                'default_author_table_cell_renderer' => 'tables/author_table/default_author_table_cell_renderer');
        
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('package') . $url . '.class.php';
            return true;
        }
        
        return false;
    }
}

?>