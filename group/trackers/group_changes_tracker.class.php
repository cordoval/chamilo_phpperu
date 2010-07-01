<?php
/**
 * $Id: group_changes_tracker.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.trackers
 */

/**
 * This class tracks the login that a user uses
 */
class GroupChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_TARGET_USER_ID = 'target_user_id';

    /**
     * Get the default properties of all aggregate trackers.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TARGET_USER_ID));
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function validate_parameters(array $parameters = array())
    {
        parent :: validate_parameters($parameters);

        if ($parameters[self :: PROPERTY_TARGET_USER_ID])
        {
            $this->set_target_user_id($parameters[self :: PROPERTY_TARGET_USER_ID]);
        }
        else
        {
            $this->set_target_user_id(0);
        }
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker($event)
    {
        $condition = new EqualityCondition('action', $event->get_name());
        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date, $event)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition('action', $event->get_name());
        return parent :: export($start_date, $end_date, $conditions);
    }

    /**
     * Inherited
     * @see MainTracker :: is_summary_tracker
     */
    function is_summary_tracker()
    {
        return false;
    }

    /**
     * @return the $user_id
     */
    public function get_target_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_TARGET_USER_ID);
    }

    /**
     * @param $user_id the $user_id to set
     */
    public function set_target_user_id($target_user_id)
    {
        $this->set_default_property(self :: PROPERTY_TARGET_USER_ID, $target_user_id);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>