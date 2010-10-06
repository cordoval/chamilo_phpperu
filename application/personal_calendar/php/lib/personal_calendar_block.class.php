<?php
/**
 * $Id: personal_calendar_block.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar
 */

require_once Path :: get_library_path() . 'block.class.php';
require_once BasicApplication :: get_application_class_lib_path('personal_calendar') . 'personal_calendar_data_manager.class.php';

/**
==============================================================================
 * This class represents a general Personal Calendar Block.
 *
 * @author Hans De bisschop
==============================================================================
 */

class PersonalCalendarBlock extends Block
{

    function get_events($from_date, $to_date)
    {
        return PersonalCalendarDataManager :: get_events($this->get_parent(), $from_data, $to_date);
    }
}
?>