<?php
/**
 * $Id: personal_calendar_120_updater.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.install
 */
require_once dirname(__FILE__) . '/../../personal_calendar_data_manager.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal calendar application.
 */
class PersonalCalendar120Updater extends Updater
{

    /**
     * Constructor
     */
    function PersonalCalendar120Updater()
    {
        parent :: __construct(PersonalCalendarDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>