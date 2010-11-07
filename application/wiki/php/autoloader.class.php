<?php
namespace application\wiki;

use common\libraries\Utilities;
use common\libraries\WebApplication;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class WikiAutoloader
{
	static function load($classname)
	{
      	$classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
            return false;
        }
        else
        {
            $classname = $classname_parts[count($classname_parts) - 1];
            array_pop($classname_parts);
            if (implode('\\', $classname_parts) != __NAMESPACE__)
            {
                return false;
            }
            else
            {
				$list = array(
					'wiki_publication' => 'wiki_publication.class.php',
					'wiki_pub_feedback' => 'wiki_pub_feedback.class.php',
					'wiki_data_manager_interface' => 'wiki_data_manager_interface.class.php',
					'wiki_data_manager' => 'wiki_data_manager.class.php',
					'wiki_gradebook_tree_menu_data_provider' => 'wiki_gradebook_tree_menu_data_provider.class.php',
					'database_wiki_data_manager' => 'data_manager/database_wiki_data_manager.class.php',
					'wiki_publication_form' => 'forms/wiki_publication_form.class.php',
					'wiki_publication_publisher' => 'publisher/wiki_publication_publisher.class.php',
					'publication_rss' => 'rss/publication_rss.class.php',
					'default_wiki_publication_table_cell_renderer' => 'tables/wiki_publication_table/default_wiki_publication_table_cell_renderer.class.php',
					'default_wiki_publication_table_column_model' => 'tables/wiki_publication_table/default_wiki_publication_table_column_model.class.php',
					'wiki_manager' => 'wiki_manager/wiki_manager.class.php',
					'wiki_evaluation' => 'wiki_manager/component/wiki_evaluation.class.php',
					'wiki_publication_creator' => 'wiki_manager/component/wiki_publication_creator.class.php',
					'wiki_publication_deleter' => 'wiki_manager/component/wiki_publication_deleter.class.php',
					'wiki_publication_updater' => 'wiki_manager/component/wiki_publication_updater.class.php',
					'wiki_publication_browser' => 'wiki_manager/component/wiki_publication_browser.class.php',
					'wiki_viewer' => 'wiki_manager/component/wiki_viewer.class.php',
					'wiki_publication_browser_table_cell_renderer' => 'wiki_manager/component/wiki_publication_browser/wiki_publication_browser_table_cell_renderer.class.php',
					'wiki_publication_browser_table_column_model' => 'wiki_manager/component/wiki_publication_browser/wiki_publication_browser_table_column_model.class.php',
					'wiki_publication_browser_table_data_provider' => 'wiki_manager/component/wiki_publication_browser/wiki_publication_browser_table_data_provider.class.php',
					'wiki_publication_browser_table' => 'wiki_manager/component/wiki_publication_browser/wiki_publication_browser_table.class.php',
					'reporting_wiki' => '../reporting/reporting_wiki.class.php',
					'wiki_reporting_block' => '../reporting/wiki_reporting_block.class.php',
					'wiki_most_edited_page_reporting_block' => '../reporting/blocks/wiki_most_edited_page_reporting_block.class.php',
					'wiki_most_visited_page_reporting_block' => '../reporting/blocks/wiki_most_visited_page_reporting_block.class.php',
					'wiki_page_most_active_users_reporting_block' => '../reporting/blocks/wiki_page_most_active_users_reporting_block.class.php',
					'wiki_page_users_contributions_reporting_block' => '../reporting/blocks/wiki_page_users_contributions_reporting_block.class.php',
					'wiki_most_reporting_template' => '../reporting/templates/wiki_most_reporting_template.class.php',
					'wiki_page_most_reporting_template' => '../reporting/templates/wiki_page_most_reporting_template.class.php',
				
								
				);  
		     
		        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        		if (key_exists($lower_case, $list))
        		{
            		$url = $list[$lower_case];
            		require_once WebApplication :: get_application_class_lib_path('wiki') . $url;
            		return true;
        		}
            }
		}
	}
}

?>