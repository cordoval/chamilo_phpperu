<?php
/**
 * $Id: admin_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package admin
 */

class AdminAutoloader
{
	static function load($classname)
	{
		if(self :: check_for_general_files($classname))
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
		$list = array('admin_block', 'admin_data_manager', 'admin_rights', 'configuration_form', 'feedback_publication', 'language_form',
					  'language', 'registration', 'remote_package', 'setting', 'system_announcement_publication_form',
					  'system_announcement_publication', 'validation');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(in_array($lower_case, $list))
		{
			require_once dirname(__FILE__) . '/lib/' . $lower_case . '.class.php';
			return true;
		}

		return false;
	}

	static function check_for_tables($classname)
	{
		$list = array('system_announcement_publication_browser_table' => 'system_announcement_publication_browser/system_announcement_publication_browser_table.class.php',
					  'whois_online_table' => 'whois_online_table/whois_online_table.class.php');

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
		$list = array('admin_manager' => 'admin_manager/admin_manager.class.php',
					  'admin_manager_component' => 'admin_manager/admin_manager_component.class.php',
					  'admin_search_form' => 'admin_manager/admin_search_form.class.php',
					  'system_announcer_multipublisher' => 'announcer/system_announcement_multipublisher.class.php',
					  'category_manager' => 'admin_category_manager.class.php',
					  'package_installer' => 'package_installer/package_installer.class.php',
					  'package_manager' => 'package_manager/package_manager.class.php',
					  'package_remover' => 'package_remover/package_remover.class.php');

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