<?php
/**
 * $Id: assessment_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment
 */

//require_once dirname(__FILE__) . '/assessment_display_component.class.php';
/**
 * This tool allows a user to publish assessments in his or her course.
 */
class AssessmentDisplay extends ComplexDisplay
{
    const ACTION_VIEW_ASSESSMENT = 'assessment_viewer';
    const ACTION_VIEW_ASSESSMENT_RESULT = 'result_viewer';
    
    const DEFAULT_ACTION = self :: ACTION_VIEW_ASSESSMENT;

    function save_answer($complex_question_id, $answer, $score)
    {
        return $this->get_parent()->save_answer($complex_question_id, $answer, $score);
    }

    function finish_assessment($total_score)
    {
        return $this->get_parent()->finish_assessment($total_score);
    }

    function change_answer_data($complex_question_id, $score, $feedback)
    {
        return $this->get_parent()->change_answer_data($complex_question_id, $score, $feedback);
    }

    function change_total_score($total_score)
    {
        return $this->get_parent()->change_total_score($total_score);
    }

    function get_current_attempt_id()
    {
        return $this->get_parent()->get_current_attempt_id();
    }

    function get_go_back_url()
    {
        return $this->get_parent()->get_go_back_url();
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_DISPLAY_ACTION;
    }
}
?>