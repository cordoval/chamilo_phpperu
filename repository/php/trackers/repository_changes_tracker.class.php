<?php
namespace repository;

use common\libraries\Utilities;
use tracking\ChangesTracker;

/**
 * $Id: repository_changes_tracker.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.trackers
 */

/**
 * This class tracks the login that a user uses
 */
class RepositoryChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>