<?php
/**
 * @package application.lib.weblcms.trackers
 */
class WeblcmsLpiAttemptInteractionTracker extends SimpleTracker
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

    function validate_parameters(array $parameters = array())
    {
        $this->set_lpi_view_id($parameters[self :: PROPERTY_LPI_VIEW_ID]);
        $this->set_interaction_id($parameters[self :: PROPERTY_INTERACTION_ID]);
        $this->set_interaction_type($parameters[self :: PROPERTY_INTERACTION_TYPE]);
        $this->set_weight($parameters[self :: PROPERTY_WEIGHT]);
        $this->set_completion_time($parameters[self :: PROPERTY_COMPLETION_TIME]);
        $this->set_correct_responses($parameters[self :: PROPERTY_CORRECT_RESPONSES]);
        $this->set_student_responses($parameters[self :: PROPERTY_STUDENT_RESPONSES]);
        $this->set_result($parameters[self :: PROPERTY_RESULT]);
        $this->set_latency($parameters[self :: PROPERTY_LATENCY]);
        $this->set_display_order($parameters[self :: PROPERTY_DISPLAY_ORDER]);
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_LPI_VIEW_ID, self :: PROPERTY_INTERACTION_ID, self :: PROPERTY_INTERACTION_TYPE, self :: PROPERTY_WEIGHT, self :: PROPERTY_COMPLETION_TIME, self :: PROPERTY_CORRECT_RESPONSES,
                self :: PROPERTY_STUDENT_RESPONSES, self :: PROPERTY_RESULT, self :: PROPERTY_LATENCY, self :: PROPERTY_DISPLAY_ORDER));
    }

    function get_lpi_view_id()
    {
        return $this->get_default_property(self :: PROPERTY_LPI_VIEW_ID);
    }

    function set_lpi_view_id($lpi_view_id)
    {
        $this->set_default_property(self :: PROPERTY_LPI_VIEW_ID, $lpi_view_id);
    }

    function get_interaction_id()
    {
        return $this->get_default_property(self :: PROPERTY_INTERACTION_ID);
    }

    function set_interaction_id($interaction_id)
    {
        $this->set_default_property(self :: PROPERTY_INTERACTION_ID, $interaction_id);
    }

    function get_interaction_type()
    {
        return $this->get_default_property(self :: PROPERTY_INTERACTION_TYPE);
    }

    function set_interaction_type($interaction_type)
    {
        $this->set_default_property(self :: PROPERTY_INTERACTION_TYPE, $interaction_type);
    }

    function get_weight()
    {
        return $this->get_default_property(self :: PROPERTY_WEIGHT);
    }

    function set_weight($weight)
    {
        $this->set_default_property(self :: PROPERTY_WEIGHT, $weight);
    }

    function get_completion_time()
    {
        return $this->get_default_property(self :: PROPERTY_COMPLETION_TIME);
    }

    function set_completion_time($completion_time)
    {
        $this->set_default_property(self :: PROPERTY_COMPLETION_TIME, $completion_time);
    }

    function get_correct_responses()
    {
        return $this->get_default_property(self :: PROPERTY_CORRECT_RESPONSES);
    }

    function set_correct_responses($correct_responses)
    {
        $this->set_default_property(self :: PROPERTY_CORRECT_RESPONSES, $correct_responses);
    }

    function get_student_responses()
    {
        return $this->get_default_property(self :: PROPERTY_STUDENT_RESPONSES);
    }

    function set_student_responses($student_responses)
    {
        $this->set_default_property(self :: PROPERTY_STUDENT_RESPONSES, $student_responses);
    }

    function get_result()
    {
        return $this->get_default_property(self :: PROPERTY_RESULT);
    }

    function set_result($result)
    {
        $this->set_default_property(self :: PROPERTY_RESULT, $result);
    }

    function get_latency()
    {
        return $this->get_default_property(self :: PROPERTY_LATENCY);
    }

    function set_latency($latency)
    {
        $this->set_default_property(self :: PROPERTY_LATENCY, $latency);
    }

    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>