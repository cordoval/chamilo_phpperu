<?php
/**
 * $Id: weblcms_lpi_attempt_interaction_tracker.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.trackers
 */
class WeblcmsLpiAttemptInteractionTracker extends MainTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_LPI_VIEW_ID = 'lpi_view_id';
    const PROPERTY_INTERACTION_ID = 'interaction_id';
    const PROPERTY_INTERACTION_TYPE = 'interaction_type';
    const PROPERTY_WEIGHT = 'weight';
    const PROPERTY_COMPLETION_TIME = 'completion_time';
    const PROPERTY_CORRECT_RESPONSES = 'correct_responses';
    const PROPERTY_STUDENT_RESPONSES = 'student_responses';
    const PROPERTY_RESULT = 'result';
    const PROPERTY_LATENCY = 'latency';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    /**
     * Constructor sets the default values
     */
    function WeblcmsLpiAttemptInteractionTracker()
    {
        parent :: MainTracker('weblcms_lpi_attempt_interaction_tracker');
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
        return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_LPI_VIEW_ID, self :: PROPERTY_INTERACTION_ID, self :: PROPERTY_INTERACTION_TYPE, self :: PROPERTY_WEIGHT, self :: PROPERTY_COMPLETION_TIME, self :: PROPERTY_CORRECT_RESPONSES, self :: PROPERTY_STUDENT_RESPONSES, self :: PROPERTY_RESULT, self :: PROPERTY_LATENCY, self :: PROPERTY_DISPLAY_ORDER));
    }

    function get_lpi_view_id()
    {
        return $this->get_property(self :: PROPERTY_LPI_VIEW_ID);
    }

    function set_lpi_view_id($lpi_view_id)
    {
        $this->set_property(self :: PROPERTY_LPI_VIEW_ID, $lpi_view_id);
    }

    function get_interaction_id()
    {
        return $this->get_property(self :: PROPERTY_INTERACTION_ID);
    }

    function set_interaction_id($interaction_id)
    {
        $this->set_property(self :: PROPERTY_INTERACTION_ID, $interaction_id);
    }

    function get_interaction_type()
    {
        return $this->get_property(self :: PROPERTY_INTERACTION_TYPE);
    }

    function set_interaction_type($interaction_type)
    {
        $this->set_property(self :: PROPERTY_INTERACTION_TYPE, $interaction_type);
    }

    function get_weight()
    {
        return $this->get_property(self :: PROPERTY_WEIGHT);
    }

    function set_weight($weight)
    {
        $this->set_property(self :: PROPERTY_WEIGHT, $weight);
    }

    function get_completion_time()
    {
        return $this->get_property(self :: PROPERTY_COMPLETION_TIME);
    }

    function set_completion_time($completion_time)
    {
        $this->set_property(self :: PROPERTY_COMPLETION_TIME, $completion_time);
    }

    function get_correct_responses()
    {
        return $this->get_property(self :: PROPERTY_CORRECT_RESPONSES);
    }

    function set_correct_responses($correct_responses)
    {
        $this->set_property(self :: PROPERTY_CORRECT_RESPONSES, $correct_responses);
    }

    function get_student_responses()
    {
        return $this->get_property(self :: PROPERTY_STUDENT_RESPONSES);
    }

    function set_student_responses($student_responses)
    {
        $this->set_property(self :: PROPERTY_STUDENT_RESPONSES, $student_responses);
    }

    function get_result()
    {
        return $this->get_property(self :: PROPERTY_RESULT);
    }

    function set_result($result)
    {
        $this->set_property(self :: PROPERTY_RESULT, $result);
    }

    function get_latency()
    {
        return $this->get_property(self :: PROPERTY_LATENCY);
    }

    function set_latency($latency)
    {
        $this->set_property(self :: PROPERTY_LATENCY, $latency);
    }

    function get_display_order()
    {
        return $this->get_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    function set_display_order($display_order)
    {
        $this->set_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    function empty_tracker($event)
    {
        $this->remove();
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>