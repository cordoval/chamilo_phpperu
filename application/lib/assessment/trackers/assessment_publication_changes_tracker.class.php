<?php

/**
 * $Id: assessment_publication_changes_tracker.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.trackers
 */


/**
 * This class tracks the login that a user uses
 */
class AssessmentPublicationChangesTracker extends DefaultTracker
{
    const CLASS_NAME = __CLASS__;

    /**
     * Constructor sets the default values
     */
    function AssessmentPublicationChangesTracker()
    {
        parent :: MainTracker('assessment_publication_changes_tracker');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
        $target = $parameters['target_id'];
        $action_user = $parameters['action_user_id'];
        $action = $parameters['event'];
        
        $this->set_user_id($action_user);
        $this->set_reference_id($target);
        $this->set_action($action);
        $this->set_date(time());
        
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

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>