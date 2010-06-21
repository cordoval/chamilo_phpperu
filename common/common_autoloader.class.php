<?php
/**
 * $Id: common_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @package common
 */
class CommonAutoloader
{
	static function load($classname)
	{
		if(self :: load_files_with_same_directory_name($classname))
		{
			return true;
		}

		if(self :: check_for_utilities_files($classname))
		{
		    return true;
		}

		if(self :: check_for_html_files($classname))
		{
			return true;
		}

		if(self :: check_for_general_files($classname))
		{
			return true;
		}

		if(self :: check_for_conditions($classname))
		{
			return true;
		}

		if(self :: check_for_special_files($classname))
		{
			return true;
		}

		return false;
	}

	static function load_files_with_same_directory_name($classname)
	{
		$list = array('authentication', 'configuration', 'database', 'datetime', 'debug', 'diagnoser', 'export', 'filecompression',
				      'filesystem', 'hashing', 'image_manipulation', 'import', 'mail', 'security', 'session', 'string', 'translation',
					  'validator', 'xml', 'webservice');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(in_array($lower_case, $list))
		{
			require_once dirname(__FILE__) . '/' . $lower_case . '/' . $lower_case . '.class.php';
			return true;
		}

		return false;
	}

	static function check_for_utilities_files($classname)
	{
	    $list = array('datetime_utilities' => 'datetime', 'debug_utilities' => 'debug', 'string_utilities' => 'string', 'xml_utilities' => 'xml');

	    $lower_case = Utilities :: camelcase_to_underscores($classname);

	    if(array_key_exists($lower_case, $list))
	    {
			require_once dirname(__FILE__) . '/' . $list[$lower_case] . '/' . $lower_case . '.class.php';
			return true;
	    }
	    else
	    {
	        return false;
	    }
	}

	static function check_for_html_files($classname)
	{
		$list = array('bbcode_parser' => 'bbcode_parser.class.php',
					  'breadcrumb_trail' => 'breadcrumb_trail.class.php',
					  'breadcrumb' => 'breadcrumb.class.php',
					  'dynamic_form_tabs_renderer' => 'tabs/dynamic_form_tabs_renderer.class.php',
					  'dynamic_tabs_renderer' => 'tabs/dynamic_tabs_renderer.class.php',
					  'dynamic_tab' => 'tabs/dynamic_tab.class.php',
					  'dynamic_action' => 'tabs/dynamic_action.class.php',
					  'dynamic_actions_tab' => 'tabs/dynamic_actions_tab.class.php',
					  'dynamic_content_tab' => 'tabs/dynamic_content_tab.class.php',
					  'dynamic_form_tab' => 'tabs/dynamic_form_tab.class.php',
					  'display' => 'display.class.php',
					  'header' => 'header.class.php',
					  'footer' => 'footer.class.php',
					  'text' => 'text.class.php',
					  'message_logger' => 'message_logger.class.php',
					  'theme' => 'layout/theme.class.php',
					  'phpbb2_template_wrapper' => 'layout/phpbb2_template_wrapper.class.php',
					  'chamilo_template' => 'layout/chamilo_template.class.php',
					  'toolbar' => 'toolbar/toolbar.class.php',
					  'toolbar_item' => 'toolbar/toolbar_item.class.php',
					  'simple_table' => 'table/simple_table.class.php',
					  'gallery_table' => 'table/gallery_table.class.php',
					  'gallery_table_from_array' => 'table/gallery_table.class.php',
					  'sortable_table' => 'table/sortable_table.class.php',
					  'sortable_table_from_array' => 'table/sortable_table.class.php',
					  'static_table_column' => 'table/static_table_column.class.php',
					  'table_column' => 'table/table_column.class.php',
					  'table_sort' => 'table/table_sort.class.php',
					  'object_table_cell_renderer' => 'table/object_table/object_table_cell_renderer.class.php',
					  'object_table_column_model' => 'table/object_table/object_table_column_model.class.php',
					  'object_table_column' => 'table/object_table/object_table_column.class.php',
					  'object_table_data_provider' => 'table/object_table/object_table_data_provider.class.php',
					  'object_table_form_action' => 'table/object_table/object_table_form_action.class.php',
					  'object_table_form_actions' => 'table/object_table/object_table_form_actions.class.php',
					  'object_table_order' => 'table/object_table/object_table_order.class.php',
					  'object_table' => 'table/object_table/object_table.class.php',
					  'gallery_object_table_cell_renderer' => 'table/gallery_object_table/gallery_object_table_cell_renderer.class.php',
					  'gallery_object_table_property_model' => 'table/gallery_object_table/gallery_object_table_property_model.class.php',
					  'gallery_object_table_property' => 'table/gallery_object_table/gallery_object_table_property.class.php',
					  'gallery_object_table_data_provider' => 'table/gallery_object_table/gallery_object_table_data_provider.class.php',
					  'gallery_object_table' => 'table/gallery_object_table/gallery_object_table.class.php',
					  'drag_and_drop_tree_menu_renderer' => 'menu/drag_and_drop_tree_menu_renderer.class.php',
					  'options_menu_renderer' => 'menu/options_menu_renderer.class.php',
					  'tree_menu_renderer' => 'menu/tree_menu_renderer.class.php',
					  'xml_tree_menu_renderer' => 'menu/xml_tree_menu_renderer.class.php',
					  'wizard_page_validator' => 'formvalidator/wizard_page_validator.class.php',
					  'form_validator' => 'formvalidator/form_validator.class.php',
					  'form_validator_page' => 'formvalidator/form_validator_page.class.php',
					  'form_validator_tab' => 'formvalidator/form_validator_tab.class.php',
					  'form_validator_html_editor' => 'formvalidator/form_validator_html_editor.class.php',
					  'form_validator_html_editor_templates' => 'formvalidator/form_validator_html_editor_templates.class.php',
		              'form_validator_html_editor_options' => 'formvalidator/form_validator_html_editor_options.class.php',
					  'html_editor_processor' => 'formvalidator/html_editor/html_editor_file_browser/html_editor_processor/html_editor_processor.class.php',
					  'action_bar_renderer' => 'action_bar/action_bar_renderer.class.php'
		);

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(key_exists($lower_case, $list))
		{
			$url = $list[$lower_case];
			require_once dirname(__FILE__) . '/html/' . $url;
			return true;
		}

		return false;
	}

	static function check_for_general_files($classname)
	{
		$list = array('application_component', 'application', 'block', 'core_application_component', 'core_application',
				      'installer', 'redirect', 'resource_manager', 'sub_manager_component', 'sub_manager', 'launcher_application', 'basic_application');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(in_array($lower_case, $list))
		{
			require_once dirname(__FILE__) . '/' . $lower_case . '.class.php';
			return true;
		}

		return false;
	}

	static function check_for_special_files($classname)
	{
		$list = array('platform_setting' =>  'configuration/platform_setting.class.php',
					  'local_setting' =>  'configuration/local_setting.class.php',
					  'array_result_set' => 'database/array_result_set.class.php',
					  'connection' => 'database/connection.class.php',
					  'object_result_set' => 'database/object_result_set.class.php',
					  'record_result_set' => 'database/record_result_set.class.php',
					  'database_alias_generator' => 'database/database_alias_generator.class.php',
					  'data_manager_interface' => 'database/data_manager_interface.class.php',
					  'data_class' => 'database/data_class.class.php',
					  'nested_tree_node' => 'database/nested_tree_node.class.php',
					  'nested_tree_database' => 'database/nested_tree_database.class.php',
					  'cookie' => 'session/cookie.class.php',
					  'request' => 'session/request.class.php',
					  'rss_icon_generator' => 'util/rss_icon_generator/rss_icon_generator.class.php',
					  'streaming_media_launcher' => 'launcher/streaming_media/streaming_media_launcher.class.php',
					  'chamilo_test_suite' => 'test/chamilo_test_suite.class.php'
		);

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(key_exists($lower_case, $list))
		{
			$url = $list[$lower_case];
			require_once dirname(__FILE__) . '/' . $url;
			return true;
		}

		return false;
	}

	static function check_for_conditions($classname)
	{
		$lower_case = Utilities :: camelcase_to_underscores($classname);
		if(strpos($lower_case, 'condition') !== false)
		{
			require_once dirname(__FILE__) . '/condition/' . $lower_case . '.class.php';
			return true;
		}

		return false;
	}
}

?>