<?php
/**
 * $Id: repository_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package repository
 */

class RepositoryAutoloader
{
	static function load($classname)
	{
		if(self :: check_for_general_files($classname))
		{
			return true;
		}

		if(self :: check_for_form_files($classname))
		{
			return true;
		}

		if(self :: check_for_tables($classname))
		{
			return true;
		}
		
		if(self :: check_for_special_files($classname))
		{
			return true;
		}
		
		if(self :: check_for_content_objects($classname))
		{
			return true;
		}
		
		return false;
	}

	static function check_for_general_files($classname)
	{
		$list = array('abstract_content_object', 'accessible_content_object', 'catalog', 'complex_content_object_item_form', 'complex_content_object_item',
					  'complex_content_object_menu', 'content_object_category_menu', 'content_object_copier', 'content_object_difference_display',
					  'content_object_difference', 'content_object_display', 'content_object_form', 'content_object_import_form', 'content_object_include_parser',
					  'content_object_metadata_catalog', 'content_object_metadata', 'content_object_pub_feedback', 'content_object_publication_attributes',
					  'content_object', 'difference_engine', 'external_repository_fedora', 'external_repository', 'external_repository_sync_info', 'quota_manager', 'repository_block',
					  'repository_data_class', 'repository_data_manager', 'repository_rights', 'user_view_rel_content_object', 'user_view');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(in_array($lower_case, $list))
		{
			require_once dirname(__FILE__) . '/lib/' . $lower_case . '.class.php';
			return true;
		}

		return false;
	}

	static function check_for_form_files($classname)
	{
		$list = array('external_repository_browser_form', 'external_repository_export_form', 'external_repository_object_browser_form', 'external_repository_import_form', 'metadata_lom_edit_form', 'metadata_lom_export_form',
					  'repository_filter_form', 'user_view_form');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(in_array($lower_case, $list))
		{
			require_once dirname(__FILE__) . '/lib/forms/' . $lower_case . '.class.php';
			return true;
		}

		return false;
	}

	static function check_for_tables($classname)
	{
		$list = array('repository_browser_table' => 'browser/repository_browser_table.class.php',
					  'template_browser_table' => 'browser/template_browser/template_browser_table.class.php',
					  'repository_shared_content_objects_browser_table' => 'browser/shared_content_objects_browser/repository_shared_content_objects_browser_table.class.php',
					  'complex_browser_table' => 'complex_browser/complex_browser_table.class.php',
					  'publication_browser_table' => 'publication_browser/publication_browser_table.class.php',
					  'recycle_bin_browser_table' => 'recycle_bin_browser/recycle_bin_browser_table.class.php',
					  'user_view_browser_table' => 'user_view_browser/user_view_browser_table.class.php',
				      'link_browser_table' => 'link_browser/link_browser_table.class.php');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(key_exists($lower_case, $list))
		{
			$url = $list[$lower_case];
			require_once dirname(__FILE__) . '/lib/repository_manager/component/' . $url;
			return true;
		}

		return false;
	}

	static function check_for_special_files($classname)
	{
		$list = array('complex_builder' => 'complex_builder/complex_builder.class.php',
					  'complex_display' => 'complex_display/complex_display.class.php',
					  'repository_category_manager' => 'category_manager/repository_category_manager.class.php',
					  'repository_category' => 'category_manager/repository_category.class.php',
					  'content_object_export' => 'export/content_object_export.class.php',
					  'content_object_import' => 'import/content_object_import.class.php',
					  'metadata_mapper' => 'metadata/metadata_mapper.class.php',
		              'base_external_repository_connector' => 'export/external_export/base_external_repository_connector.class.php',
					  'rest_external_repository_connector' => 'export/external_export/rest_external_repository_connector.class.php',
				      'fedora_external_repository_connector' => 'export/external_export/fedora/fedora_external_repository_connector.class.php',
				      
				      'repository_manager_external_repository_component' => 'repository_manager/component/external_repository_component.class.php',
            		  'repository_manager_external_repository_browser_component' => 'repository_manager/component/external_repository_browser.class.php',
            		  'repository_manager_external_repository_export_component' => 'repository_manager/component/external_repository_export.class.php',
            		  'repository_manager_external_repository_import_component' => 'repository_manager/component/external_repository_import.class.php',
					  'repository_manager_external_repository_list_objects_component' => 'repository_manager/component/external_repository_list_objects.class.php',
					  'repository_manager_metadata_component' => 'repository_manager/component/metadata_component.class.php',
		
					  'repository_manager' => 'repository_manager/repository_manager.class.php',
					  'repository_manager_component' => 'repository_manager/repository_manager_component.class.php',
					  'repository_search_form' => 'repository_manager/repository_search_form.class.php',
					  'publisher_wizard' => 'repository_manager/component/publisher_wizard/publisher_wizard.class.php');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(key_exists($lower_case, $list))
		{
			$url = $list[$lower_case];
			require_once dirname(__FILE__) . '/lib/' . $url;
			return true;
		}

		return false;
	}
	
	static $content_objects;
	
	static function check_for_content_objects($classname)
	{
		$dir = dirname(__FILE__) . '/lib/content_object/';
		
		if(!self :: $content_objects)
		{
			self :: $content_objects = Filesystem :: get_directory_content($dir, Filesystem :: LIST_DIRECTORIES, false);
		}
		
		$lower_case = Utilities :: camelcase_to_underscores($classname);
		
		if(in_array($lower_case, self :: $content_objects))
		{
			require_once $dir . $lower_case . '/' . $lower_case . '.class.php';
		}
	}
}

?>