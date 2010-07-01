<?php
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
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>