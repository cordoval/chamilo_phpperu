<?php
/**
 * $Id: user_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package user
 */

class UserAutoloader
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

		return false;
	}

	static function check_for_general_files($classname)
	{
		$list = array('buddy_list_category', 'buddy_list_item', 'buddy_list', 'chat_manager', 'chat_message', 'user_block', 'user_data_manager',
					  'user_menu', 'user_quota', 'user_rights_template', 'user', 'user_setting');

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
		$list = array('account_form', 'buddy_list_category_form', 'buddy_list_item_form', 'register_form', 'user_export_form', 'user_form',
					  'user_import_form', 'user_quota_form', 'user_role_manager_form', 'user_search_form');

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
		$list = array('admin_user_browser_table' => 'admin_user_browser/admin_user_browser_table.class.php', 'user_approval_browser_table' => 'user_approval_browser/user_approval_browser_table.class.php');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(key_exists($lower_case, $list))
		{
			$url = $list[$lower_case];
			require_once dirname(__FILE__) . '/lib/user_manager/component/' . $url;
			return true;
		}

		return false;
	}

	static function check_for_special_files($classname)
	{
		$list = array('user_manager' => 'user_manager/user_manager.class.php',
					  'user_manager_component' => 'user_manager/user_manager_component.class.php',
					  'user_validator' => '../validator/user_validator.class.php',
					  'visit_tracker' => '../trackers/visit_tracker.class.php');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(key_exists($lower_case, $list))
		{
			$url = $list[$lower_case];
			require_once dirname(__FILE__) . '/lib/' . $url;
			return true;
		}

		return false;
	}
}

?>