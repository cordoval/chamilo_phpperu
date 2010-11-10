<?php

namespace application\personal_calendar;

use common\libraries\Utilities;
use common\libraries\Webapplication;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    public static $class_name;

    static function load($classname)
    {
       self :: $class_name = $classname;
        $list = array(
            'personal_calendar_event' => 'personal_calendar_event.class.php',
            'personal_calendar_data_manager' => 'personal_calendar_data_manager.class.php',
            'personal_calendar_event_parser' => 'personal_calendar_event_parser.class.php',
            'personal_calendar_connector' => 'personal_calendar_connector.class.php',
            'personal_calendar_weblcms_connector' => 'connector/personal_calendar_weblcms_connector.class.php',
            'personal_calendar_data_manager' => 'personal_calendar_data_manager.class.php',
            'personal_calendar_publication' => 'personal_calendar_publication.class.php',
            'personal_calendar_publication_user' => 'personal_calendar_publication_user.class.php',
            'personal_calendar_publication_group' => 'personal_calendar_publication_group.class.php',
            'personal_calendar_data_manager_interface' => 'personal_calendar_data_manager_interface.class.php',
            'personal_calendar_publication_form' => 'personal_calendar_publication_form.class.php',
            'personal_calendar_renderer' => 'personal_calendar_renderer.class.php',
            'personal_calendar_manager' => 'personal_calendar_manager/personal_calendar_manager.class.php',
			'personal_calendar_rights' => 'personal_calendar_rights.class.php',
        	'personal_calendar_publisher' => 'publisher/personal_calendar_publisher.class.php');
        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('personal_calendar') . $url;
            return true;
        }

        return false;
    }

}

?>