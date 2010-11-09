<?php

namespace application\personal_calendar;

use common\libraries\Utilities;

/**
 * $Id: personal_calendar_publication_changes_tracker.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.trackers
 */

/**
 * This class tracks the login that a user uses
 */
class PersonalCalendarPublicationChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>