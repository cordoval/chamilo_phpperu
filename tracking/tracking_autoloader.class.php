<?php
/**
 * $Id: tracking_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package tracking
 */

class TrackingAutoloader
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
		$list = array('archive_controller_item', 'default_tracker', 'event_rel_tracker', 'event', 'events', 'main_tracker', 'tracker_registration',
					  'tracker_setting', 'tracking_data_manager', 'tracker', 'aggregate_tracker', 'simple_tracker');

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
		$list = array('event_browser_table' => 'admin_event_browser/event_browser_table.class.php');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(key_exists($lower_case, $list))
		{
			$url = $list[$lower_case];
			require_once dirname(__FILE__) . '/lib/tracking_manager/component/' . $url;
			return true;
		}

		return false;
	}

	static function check_for_special_files($classname)
	{
		$list = array('tracking_manager' => 'tracking_manager/tracking_manager.class.php',
					  'tracking_manager_component' => 'tracking_manager/tracking_manager_component.class.php',
					  'archive_wizard' => 'tracking_manager/component/wizards/archive_wizard.class.php');

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