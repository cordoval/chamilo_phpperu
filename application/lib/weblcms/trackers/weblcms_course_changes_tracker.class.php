<?php
/**
 * $Id: weblcms_course_changes_tracker.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.trackers
 */


/**
 * This class tracks the login that a user uses
 */
class WeblcmsCourseChangesTracker extends DefaultTracker
{
    const CLASS_NAME = __CLASS__;
    
    // Can be used for subscribsion of users / classes
    const PROPERTY_TARGET_REFERENCE_ID = 'target_reference_id';

    /**
     * Constructor sets the default values
     */
    function WeblcmsCourseChangesTracker()
    {
        parent :: MainTracker('weblcms_course_changes_tracker');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
        $target = $parameters['target_id'];
        $target_reference = $parameters['target_reference_id'];
        $action_user = $parameters['action_user_id'];
        $action = $parameters['event'];
        
        $this->set_user_id($action_user);
        $this->set_reference_id($target);
        $this->set_action($action);
        $this->set_date(time());
        
        if ($target_reference)
            $this->set_target_reference_id($target_reference);
        else
            $this->set_target_reference_id(0);
        
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
        return array_merge(parent :: get_property_names(), array(self :: PROPERTY_TARGET_REFERENCE_ID));
    }

    /**
     * Get's the reference_id of the default tracker
     * @return int $reference_id the reference_id
     */
    function get_target_reference_id()
    {
        return $this->get_property(self :: PROPERTY_TARGET_REFERENCE_ID);
    }

    /**
     * Sets the target_reference_id of the default tracker
     * @param int $target_reference_id the target_reference_id
     */
    function set_target_reference_id($target_reference_id)
    {
        $this->set_property(self :: PROPERTY_TARGET_REFERENCE_ID, $target_reference_id);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>