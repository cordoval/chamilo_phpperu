<?php
/**
 * $Id: weblcms_lp_attempt_tracker.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.trackers
 */
class WeblcmsLpAttemptTracker extends MainTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_LP_ID = 'lp_id';
    const PROPERTY_PROGRESS = 'progress';

    /**
     * Constructor sets the default values
     */
    function WeblcmsLpAttemptTracker()
    {
        parent :: MainTracker('weblcms_lp_attempt_tracker');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
        foreach ($parameters as $key => $parameter)
        {
            if ($key != 'event' && $key != 'id')
                $this->set_property($key, $parameter);
        }
        
        $this->create();
        
        return $this;
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
    function get_default_property_names()
    {
        return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_USER_ID, self :: PROPERTY_COURSE_ID, self :: PROPERTY_LP_ID, self :: PROPERTY_PROGRESS));
    }

    function get_user_id()
    {
        return $this->get_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_course_id()
    {
        return $this->get_property(self :: PROPERTY_COURSE_ID);
    }

    function set_course_id($course_id)
    {
        $this->set_property(self :: PROPERTY_COURSE_ID, $course_id);
    }

    function get_lp_id()
    {
        return $this->get_property(self :: PROPERTY_LP_ID);
    }

    function set_lp_id($lp_id)
    {
        $this->set_property(self :: PROPERTY_LP_ID, $lp_id);
    }

    function get_progress()
    {
        return $this->get_property(self :: PROPERTY_PROGRESS);
    }

    function set_progress($progress)
    {
        $this->set_property(self :: PROPERTY_PROGRESS, $progress);
    }

    function empty_tracker($event)
    {
        $this->remove();
    }

    function delete()
    {
        $succes = parent :: delete();
        
        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $this->get_id());
        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        foreach ($trackers as $tracker)
            $succes &= $tracker->delete();
        
        return $succes;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>