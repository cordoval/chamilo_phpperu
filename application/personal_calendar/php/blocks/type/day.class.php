<?php
namespace application\personal_calendar;

use common\libraries\WebApplication;
use common\libraries\Utilities;
use common\libraries\Request;

/**
 * $Id: day.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.lib.personal_calendar.block
 */
require_once WebApplication :: get_application_class_path('personal_calendar') . 'blocks/personal_calendar_block.class.php';
require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'renderer/personal_calendar_mini_day_renderer.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalCalendarDay extends PersonalCalendarBlock
{

    function display_content()
    {
        $configuration = $this->get_configuration();

        $hour_step = $configuration['hour_step'];
        $time_start = $configuration['time_start'];
        $time_end = $configuration['time_end'];

        $html = array();
        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $minidaycalendar = new PersonalCalendarMiniDayRenderer($this, $time, $hour_step, $time_start, $time_end, $this->get_link_target());
        $html[] = $minidaycalendar->render();
        return implode("\n", $html);
    }
}
?>
