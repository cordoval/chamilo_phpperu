<?php
namespace home;
use common\libraries\Utilities;
use tracking\ChangesTracker;
/**
 * @package home.trackers
 */
class HomeChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>