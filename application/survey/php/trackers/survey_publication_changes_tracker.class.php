<?php
namespace application\survey;

use tracking\ChangesTracker;

/**
 * @package application.lib.survey.trackers
 */
class SurveyPublicationChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>