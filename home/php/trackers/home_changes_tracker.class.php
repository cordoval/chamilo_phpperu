<?php
namespace home;
use common\libraries\Utilities;
/**
 * @package home.trackers
 */
class HomeChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(array_pop(explode('\\', self :: CLASS_NAME)));
        //return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>