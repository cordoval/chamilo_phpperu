<?php
/**
 * @package admin.trackers
 */
class OnlineTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_LAST_ACCESS_DATE = 'last_access_date';

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_USER_ID, self :: PROPERTY_LAST_ACCESS_DATE);
    }

    function validate_parameters(array $parameters = array())
    {
        $user = $parameters['user'];

        $this->remove_user($user);

        $time = time();
        $active_time = PlatformSetting :: get('timelimit');
        $past_time = strtotime('-' . $active_time . ' seconds', $time);
        $this->empty_tracker_before_date($past_time);

        $this->set_user_id($user);
        $this->set_last_access_date($time);
    }

    function empty_tracker($event)
    {
        return $this->remove();
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker_before_date($date)
    {
        $condition = new InEqualityCondition(self :: PROPERTY_LAST_ACCESS_DATE, InEqualityCondition :: LESS_THAN_OR_EQUAL, $date);
        return $this->remove($condition);
    }

    function remove_user($user_id)
    {
        $condition = new EqualityCondition(self :: PROPERTY_USER_ID, $user_id);
        return $this->remove($condition);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_last_access_date()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_ACCESS_DATE);
    }

    function set_last_access_date($last_access_date)
    {
        $this->set_default_property(self :: PROPERTY_LAST_ACCESS_DATE, $last_access_date);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>