<?php

namespace application\personal_messenger;

use tracking\ChangesTracker;
use common\libraries\Utilities;

/**
 * $Id: personal_messenger_publication_changes_tracker.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.trackers
 */

/**
 * This class tracks the login that a user uses
 */
class PersonalMessengerPublicationChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>