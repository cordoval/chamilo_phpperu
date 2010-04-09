<?php
/**
 * $Id: reporting_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package reporting
 */

class ReportingAutoloader
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
		$list = array('reporting_block_layout', 'reporting_block', 'reporting_blocks', 'reporting_data_manager', 'reporting_exporter', 'reporting_formatter',
					  'reporting_template_registration', 'reporting_block_registration', 'reporting_template', 'reporting_templates', 'reporting');

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
		$list = array('reporting_template_registration_form');

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
		$list = array('reporting_template_registration_browser_table' => 'reporting_template_registration_browser_table/reporting_template_registration_browser_table.class.php');

		$lower_case = Utilities :: camelcase_to_underscores($classname);

		if(key_exists($lower_case, $list))
		{
			$url = $list[$lower_case];
			require_once dirname(__FILE__) . '/lib/reporting_manager/component/' . $url;
			return true;
		}

		return false;
	}

	static function check_for_special_files($classname)
	{
		$list = array('reporting_manager' => 'reporting_manager/reporting_manager.class.php',
					  'reporting_manager_component' => 'reporting_manager/reporting_manager_component.class.php',
					  'reporting_validator' => '../validator/reporting_validator.class.php',
					  'reporting_chart_formatter' => 'formatters/reporting_chart_formatter.class.php');

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