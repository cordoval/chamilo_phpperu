<?php
/**
 * @package application.lib.weblcms.trackers
 */
class WeblcmsLpiAttemptTracker extends SimpleTracker
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

    function validate_parameters(array $parameters = array())
    {
        $this->set_lp_item_id($parameters[self :: PROPERTY_LP_ITEM_ID]);
        $this->set_lp_view_id($parameters[self :: PROPERTY_LP_VIEW_ID]);
        $this->set_start_time($parameters[self :: PROPERTY_START_TIME]);
        $this->set_total_time($parameters[self :: PROPERTY_TOTAL_TIME]);
        $this->set_score($parameters[self :: PROPERTY_SCORE]);
        $this->set_status($parameters[self :: PROPERTY_STATUS]);
        $this->set_lesson_location($parameters[self :: PROPERTY_LESSON_LOCATION]);
        $this->set_suspend_data($parameters[self :: PROPERTY_SUSPEND_DATA]);
        $this->set_max_score($parameters[self :: PROPERTY_MAX_SCORE]);
        $this->set_min_score($parameters[self :: PROPERTY_MIN_SCORE]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_LP_VIEW_ID, self :: PROPERTY_START_TIME, self :: PROPERTY_LP_ITEM_ID, self :: PROPERTY_TOTAL_TIME, self :: PROPERTY_SCORE, self :: PROPERTY_STATUS, self :: PROPERTY_LESSON_LOCATION,
                self :: PROPERTY_SUSPEND_DATA, self :: PROPERTY_MAX_SCORE, self :: PROPERTY_MIN_SCORE));
    }

    function get_lp_view_id()
    {
        return $this->get_default_property(self :: PROPERTY_LP_VIEW_ID);
    }

    function set_lp_view_id($lp_view_id)
    {
        $this->set_default_property(self :: PROPERTY_LP_VIEW_ID, $lp_view_id);
    }

    function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    function set_start_time($start_time)
    {
        $this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
    }

    function get_lp_item_id()
    {
        return $this->get_default_property(self :: PROPERTY_LP_ITEM_ID);
    }

    function set_lp_item_id($lp_item_id)
    {
        $this->set_default_property(self :: PROPERTY_LP_ITEM_ID, $lp_item_id);
    }

    function get_total_time()
    {
        return $this->get_default_property(self :: PROPERTY_TOTAL_TIME);
    }

    function set_total_time($total_time)
    {
        $this->set_default_property(self :: PROPERTY_TOTAL_TIME, $total_time);
    }

    function get_score()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE);
    }

    function set_score($score)
    {
        $this->set_default_property(self :: PROPERTY_SCORE, $score);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_lesson_location()
    {
        return $this->get_default_property(self :: PROPERTY_LESSON_LOCATION);
    }

    function set_lesson_location($lesson_location)
    {
        $this->set_default_property(self :: PROPERTY_LESSON_LOCATION, $lesson_location);
    }

    function get_suspend_data()
    {
        return $this->get_default_property(self :: PROPERTY_SUSPEND_DATA);
    }

    function set_suspend_data($suspend_data)
    {
        $this->set_default_property(self :: PROPERTY_SUSPEND_DATA, $suspend_data);
    }

    function get_min_score()
    {
        return $this->get_default_property(self :: PROPERTY_MIN_SCORE);
    }

    function set_min_score($min_score)
    {
        $this->set_default_property(self :: PROPERTY_MIN_SCORE, $min_score);
    }

    function get_max_score()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_SCORE);
    }

    function set_max_score($max_score)
    {
        $this->set_default_property(self :: PROPERTY_MAX_SCORE, $max_score);
    }

    function delete()
    {
        $succes = parent :: delete();

        $condition = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID, $this->get_id());
        $dummy = new WeblcmsLearningPathQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        foreach ($trackers as $tracker)
        {
            $succes &= $tracker->delete();
        }

        return $succes;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>