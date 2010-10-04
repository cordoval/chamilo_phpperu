<?php
/**
 * This class is a abstract class for user tracking
 *
 * @package users.lib.trackers
 */

abstract class UserTracker extends AggregateTracker
{
    const CLASS_NAME = __CLASS__;

    const TYPE_BROWSER = 'browser';
    const TYPE_COUNTRY = 'country';
    const TYPE_OS = 'os';
    const TYPE_PROVIDER = 'provider';
    const TYPE_REFERER = 'referer';

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>