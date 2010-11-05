<?php
namespace application\weblcms;

use common\libraries\Utilities;

/**
 * @package application.lib.weblcms.trackers
 */
class WeblcmsPublicationChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>