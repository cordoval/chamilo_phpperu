<?php

namespace application\personal_calendar;

use common\libraries\WebApplication;
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

    function display_content()
    {
        $html = array();

        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time, $this->get_link_target());
        $html[] = $minimonthcalendar->render();

        return implode("\n", $html);
    }
}
?>
