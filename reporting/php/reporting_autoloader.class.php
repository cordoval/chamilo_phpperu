<?php
namespace reporting;

use common\libraries\Utilities;
/**
 * $Id: reporting_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package reporting
 */

class ReportingAutoloader
{
    public static $class_name;

    static function load($classname)
    {
        $classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
            return false;
        }
        else
        {
            self :: $class_name = $classname_parts[count($classname_parts) - 1];
            array_pop($classname_parts);
            if (implode('\\', $classname_parts) != __NAMESPACE__)
            {
                return false;
            }
        }

        if (self :: check_for_general_files())
        {
            return true;
        }

        if (self :: check_for_form_files())
        {
            return true;
        }

        if (self :: check_for_tables())
        {
            return true;
        }

        if (self :: check_for_special_files())
        {
            return true;
        }

        return false;
    }

    static function check_for_general_files()
    {
        $list = array(
                'reporting_block_layout', 'reporting_block', 'reporting_blocks', 'reporting_data_manager', 'reporting_exporter', 'reporting_formatter', 'reporting_template_registration', 'reporting_block_registration',
                'reporting_template', 'reporting_templates', 'reporting', 'reporting_template_viewer');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (in_array($lower_case, $list))
        {
            require_once dirname(__FILE__) . '/lib/' . $lower_case . '.class.php';
            return true;
        }

        return false;
    }

    static function check_for_form_files()
    {
        $list = array('reporting_template_registration_form');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (in_array($lower_case, $list))
        {
            require_once dirname(__FILE__) . '/lib/forms/' . $lower_case . '.class.php';
            return true;
        }

        return false;
    }

    static function check_for_tables()
    {
        $list = array('reporting_template_registration_browser_table' => 'reporting_template_registration_browser_table/reporting_template_registration_browser_table.class.php');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/lib/reporting_manager/component/' . $url;
            return true;
        }

        return false;
    }

    static function check_for_special_files()
    {
        $list = array('reporting_manager' => 'reporting_manager/reporting_manager.class.php', 'reporting_validator' => '../validator/reporting_validator.class.php', 'reporting_chart_formatter' => 'formatters/reporting_chart_formatter.class.php');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/lib/' . $url;
            return true;
        }

        return false;
    }
}

?>