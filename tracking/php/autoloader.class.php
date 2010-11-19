<?php

namespace tracking;

use common\libraries\Utilities;

/**
 * $Id: tracking_autoloader.class.php 236 2009-11-16 12:56:59Z scaramanga $
 * @author vanpouckesven
 * @package tracking
 */
class Autoloader
{

    public static $class_name;

    static function load($classname)
    {
        self :: $class_name = $classname;


        if (self :: check_for_general_files())
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
        $list = array('archive_controller_item', 'event_rel_tracker', 'event', 'events', 'tracker_registration', 'tracker_setting', 'tracking_data_manager', 'tracker', 'aggregate_tracker', 'simple_tracker', 'changes_tracker', 'tracking_rights');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (in_array($lower_case, $list))
        {
            require_once dirname(__FILE__) . '/lib/' . $lower_case . '.class.php';
            return true;
        }

        return false;
    }

    static function check_for_tables()
    {
        $list = array('event_browser_table' => 'admin_event_browser/event_browser_table.class.php');

        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/lib/tracking_manager/component/' . $url;
            return true;
        }

        return false;
    }

    static function check_for_special_files()
    {
        $list = array('tracking_manager' => 'tracking_manager/tracking_manager.class.php', 'tracking_manager_component' => 'tracking_manager/tracking_manager_component.class.php', 'archive_wizard' => 'tracking_manager/component/wizards/archive_wizard.class.php');

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