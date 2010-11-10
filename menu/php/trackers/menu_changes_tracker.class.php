<?php
namespace menu;
use common\libraries\Utilities;
/**
 * $Id: menu_changes_tracker.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.trackers
 */

/**
 * This class tracks the login that a user uses
 */
class MenuChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>