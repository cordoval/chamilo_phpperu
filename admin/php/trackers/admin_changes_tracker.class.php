<?php
namespace admin;
use common\libraries\Utilities;
use tracking\ChangesTracker;
/**
 * @package admin.trackers
 */
class AdminChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>