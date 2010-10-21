<?php

namespace personal_calendar;

use common\libraries\Webapplication;
use common\libraries\Utilities;
use common\libraries\Request;


/**
 * $Id: month.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.lib.personal_calendar.block
 */
//require_once Path :: get_library_path() . 'utilities.class.php';
require_once WebApplication :: get_application_class_path('personal_calendar') . '/blocks/personal_calendar_block.class.php';
require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'renderer/personal_calendar_mini_month_renderer.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalCalendarMonth extends PersonalCalendarBlock
{

    /*
	 * Inherited
	 */
    function as_html()
    {
        $html = array();

        $html[] = $this->display_header();

        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time);
        $html[] = $minimonthcalendar->render();

        $html[] = $this->display_footer();

        return implode("\n", $html);
    }
}
?>