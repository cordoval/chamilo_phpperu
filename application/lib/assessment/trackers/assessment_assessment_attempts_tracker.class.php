<?php
/**
 * @package application.lib.assessment.trackers
 */

class AssessmentAssessmentAttemptsTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_ASSESSMENT_ID = 'assessment_id';
    const PROPERTY_DATE = 'date';
    const PROPERTY_TOTAL_SCORE = 'total_score';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_TOTAL_TIME = 'total_time';

    function validate_parameters(array $parameters = array())
    {
        $status = $parameters[self :: PROPERTY_STATUS];

        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_assessment_id($parameters[self :: PROPERTY_ASSESSMENT_ID]);
        $this->set_start_time(time());

        if ($status)
        {
            $this->set_status($status);
        }

        $this->set_date(time());
        $this->set_total_score($parameters[self :: PROPERTY_TOTAL_SCORE]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_ASSESSMENT_ID, self :: PROPERTY_DATE, self :: PROPERTY_TOTAL_SCORE, self :: PROPERTY_STATUS, self :: PROPERTY_START_TIME, self :: PROPERTY_TOTAL_TIME));
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_assessment_id()
    {
        return $this->get_default_property(self :: PROPERTY_ASSESSMENT_ID);
    }

    function set_assessment_id($assessment_id)
    {
        $this->set_default_property(self :: PROPERTY_ASSESSMENT_ID, $assessment_id);
    }

    function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    function set_date($date)
    {
        $this->set_default_property(self :: PROPERTY_DATE, $date);
    }

    function get_total_score()
    {
        return $this->get_default_property(self :: PROPERTY_TOTAL_SCORE);
    }

    function set_total_score($total_score)
    {
        $this->set_default_property(self :: PROPERTY_TOTAL_SCORE, $total_score);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    function set_start_time($start_time)
    {
        $this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
    }

    function get_total_time()
    {
        return $this->get_default_property(self :: PROPERTY_TOTAL_TIME);
    }

    function set_total_time($total_time)
    {
        $this->set_default_property(self :: PROPERTY_TOTAL_TIME, $total_time);
    }

    function get_times_taken($publication, $user_id = null)
    {
        $condition = new EqualityCondition(self :: PROPERTY_ASSESSMENT_ID, $publication->get_id());

        if ($user_id)
        {
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = new EqualityCondition(self :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);
        }

        $trackers = $this->retrieve_tracker_items($condition);
        return count($trackers);
    }

    function delete()
    {
        parent :: delete();

        $condition = new EqualityCondition(AssessmentQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->get_id());
        $trackers = $this->get_data_manager()->retrieve_tracker_items(AssessmentQuestionAttemptsTracker :: get_table_name(), AssessmentQuestionAttemptsTracker :: CLASS_NAME, $condition);

        foreach ($trackers as $tracker)
        {
            $tracker->delete();
        }
    }

    function get_average_score($publication, $user_id = null)
    {
        $condition = new EqualityCondition(self :: PROPERTY_ASSESSMENT_ID, $publication->get_id());

        if ($user_id)
        {
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = new EqualityCondition(self :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);
        }

        $trackers = $this->retrieve_tracker_items($condition);
        $num = count($trackers);

        foreach ($trackers as $tracker)
        {
            $total_score += $tracker->get_total_score();
        }

        $total_score = round($total_score / $num, 2);
        return $total_score;

    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>