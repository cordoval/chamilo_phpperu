<?php

namespace application\profiler;

use tracking\ChangesTracker;
use common\libraries\Utilities;

/**
 * @package application.profiler.trackers
 */
class ProfilerPublicationChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>