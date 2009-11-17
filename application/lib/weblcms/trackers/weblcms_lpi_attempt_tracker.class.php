<?php
/**
 * $Id: weblcms_lpi_attempt_tracker.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.trackers
 */
class WeblcmsLpiAttemptTracker extends MainTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_LP_ITEM_ID = 'lp_item_id';
    const PROPERTY_LP_VIEW_ID = 'lp_view_id';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_TOTAL_TIME = 'total_time';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_LESSON_LOCATION = 'lesson_location';
    const PROPERTY_SUSPEND_DATA = 'suspend_data';
    const PROPERTY_MAX_SCORE = 'max_score';
    const PROPERTY_MIN_SCORE = 'min_score';

    /**
     * Constructor sets the default values
     */
    function WeblcmsLpiAttemptTracker()
    {
        parent :: MainTracker('weblcms_lpi_attempt_tracker');
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
        return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_LP_VIEW_ID, self :: PROPERTY_START_TIME, self :: PROPERTY_LP_ITEM_ID, self :: PROPERTY_TOTAL_TIME, self :: PROPERTY_SCORE, self :: PROPERTY_STATUS, self :: PROPERTY_LESSON_LOCATION, self :: PROPERTY_SUSPEND_DATA, self :: PROPERTY_MAX_SCORE, self :: PROPERTY_MIN_SCORE));
    }

    function get_lp_view_id()
    {
        return $this->get_property(self :: PROPERTY_LP_VIEW_ID);
    }

    function set_lp_view_id($lp_view_id)
    {
        $this->set_property(self :: PROPERTY_LP_VIEW_ID, $lp_view_id);
    }

    function get_start_time()
    {
        return $this->get_property(self :: PROPERTY_START_TIME);
    }

    function set_start_time($start_time)
    {
        $this->set_property(self :: PROPERTY_START_TIME, $start_time);
    }

    function get_lp_item_id()
    {
        return $this->get_property(self :: PROPERTY_LP_ITEM_ID);
    }

    function set_lp_item_id($lp_item_id)
    {
        $this->set_property(self :: PROPERTY_LP_ITEM_ID, $lp_item_id);
    }

    function get_total_time()
    {
        return $this->get_property(self :: PROPERTY_TOTAL_TIME);
    }

    function set_total_time($total_time)
    {
        $this->set_property(self :: PROPERTY_TOTAL_TIME, $total_time);
    }

    function get_score()
    {
        return $this->get_property(self :: PROPERTY_SCORE);
    }

    function set_score($score)
    {
        $this->set_property(self :: PROPERTY_SCORE, $score);
    }

    function get_status()
    {
        return $this->get_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_property(self :: PROPERTY_STATUS, $status);
    }

    function get_lesson_location()
    {
        return $this->get_property(self :: PROPERTY_LESSON_LOCATION);
    }

    function set_lesson_location($lesson_location)
    {
        $this->set_property(self :: PROPERTY_LESSON_LOCATION, $lesson_location);
    }

    function get_suspend_data()
    {
        return $this->get_property(self :: PROPERTY_SUSPEND_DATA);
    }

    function set_suspend_data($suspend_data)
    {
        $this->set_property(self :: PROPERTY_SUSPEND_DATA, $suspend_data);
    }

    function get_min_score()
    {
        return $this->get_property(self :: PROPERTY_MIN_SCORE);
    }

    function set_min_score($min_score)
    {
        $this->set_property(self :: PROPERTY_MIN_SCORE, $min_score);
    }

    function get_max_score()
    {
        return $this->get_property(self :: PROPERTY_MAX_SCORE);
    }

    function set_max_score($max_score)
    {
        $this->set_property(self :: PROPERTY_MAX_SCORE, $max_score);
    }

    function empty_tracker($event)
    {
        $this->remove();
    }

    function delete()
    {
        $succes = parent :: delete();
        
        $condition = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID, $this->get_id());
        $dummy = new WeblcmsLearningPathQuestionAttemptsTracker();
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