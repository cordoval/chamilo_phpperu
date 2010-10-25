<?php
namespace rights;

use common\libraries\Utilities;

/**
 * $Id: roles_right_changes_tracker.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.trackers.tracker_tables
 */


/**
 * This class tracks the login that a user uses
 */
class RightsTemplatesRightChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(array_pop(explode('\\', self :: CLASS_NAME)));
        //return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>