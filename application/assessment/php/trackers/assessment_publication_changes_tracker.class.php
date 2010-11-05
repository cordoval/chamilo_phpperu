<?php

namespace application\assessment;

use tracking\ChangesTracker;

/**
 * @package application.lib.assessment.trackers
 */
class AssessmentPublicationChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>