<?php
/**
 * $Id: group_changes_tracker.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.trackers
 */


/**
 * This class tracks the login that a user uses
 */
class GroupChangesTracker extends DefaultTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_TARGET_USER_ID = 'target_user_id';

    /**
     * Constructor sets the default values
     */
    function GroupChangesTracker()
    {
        parent :: MainTracker('group_changes');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
        $target = $parameters['target_group_id'];
        $target_user = $parameters['target_user_id'];
        $action_user = $parameters['action_user_id'];
        $action = $parameters['event'];
        
        $this->set_user_id($action_user);
        $this->set_reference_id($target);
        $this->set_action($action);
        $this->set_date(time());
        
        if ($target_user)
            $this->set_target_user_id($target_user);
        else
            $this->set_target_user_id(0);
        
        $this->create();
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
     * Inherited
     */
    function get_property_names()
    {
        return array_merge(parent :: get_property_names(), array(self :: PROPERTY_TARGET_USER_ID));
    }

    /**
     * Get's the user_id of the default tracker
     * @return int $user_id the user_id
     */
    function get_target_user_id()
    {
        return $this->get_property(self :: PROPERTY_TARGET_USER_ID);
    }

    /**
     * Sets the target_user_id of the default tracker
     * @param int $target_user_id the target_user_id
     */
    function set_target_user_id($target_user_id)
    {
        $this->set_property(self :: PROPERTY_TARGET_USER_ID, $target_user_id);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>