<?php
/**
 * @author Hans De bisschop
 */
require_once BasicApplication :: get_application_class_lib_path('personal_calendar') . 'personal_calendar_data_manager.class.php';

class PersonalCalendarBlock extends Block
{

    function get_events($from_date, $to_date)
    {
        return PersonalCalendarDataManager :: get_events($this->get_parent(), $from_data, $to_date);
    }
}
?>