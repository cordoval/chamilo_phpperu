<?php
namespace user;

use common\libraries\Utilities;
use tracking\ChangesTracker;
/**
 * $Id: user_changes_tracker.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package users.lib.trackers
 */

/**
 * This class tracks the login that a user uses
 */
class UserChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(array_pop(explode('\\', self :: CLASS_NAME)));
        //return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>