<?php
namespace repository\content_object\assessment;

use common\libraries\Request;

use repository\ComplexDisplay;
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
    const ACTION_VIEW_ASSESSMENT_RESULT = 'results_viewer';

    const DEFAULT_ACTION = self :: ACTION_VIEW_ASSESSMENT;

    function __construct($parent)
    {
        parent :: __construct($parent);
        $this->register_parameters();
    }

    function save_assessment_answer($complex_question_id, $answer, $score)
    {
        return $this->get_parent()->save_assessment_answer($complex_question_id, $answer, $score);
    }

    function save_assessment_result($total_score)
    {
        return $this->get_parent()->save_assessment_result($total_score);
    }

    function change_answer_data($complex_question_id, $score, $feedback)
    {
        return $this->get_parent()->change_answer_data($complex_question_id, $score, $feedback);
    }

    function change_total_score($total_score)
    {
        return $this->get_parent()->change_total_score($total_score);
    }

    function get_assessment_current_attempt_id()
    {
        return $this->get_parent()->get_assessment_current_attempt_id();
    }

    function get_assessment_question_attempts()
    {
        return $this->get_parent()->get_assessment_question_attempts();
    }

    function get_assessment_question_attempt($complex_content_object_question_id)
    {
        return $this->get_parent()->get_assessment_question_attempt($complex_content_object_question_id);
    }

    function get_assessment_back_url()
    {
        return $this->get_parent()->get_assessment_back_url();
    }

    function get_assessment_continue_url()
    {
        return $this->get_parent()->get_assessment_continue_url();
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    /**
     * @return FeedbackDisplayConfiguration
     */
    function get_feedback_display_configuration()
    {
        return $this->get_parent()->get_assessment_feedback_configuration();
    }

    /**
     * @return number
     */
    function get_feedback_type()
    {
        return $this->get_feedback_display_configuration()->get_feedback_type();
    }

    /**
     * @return boolean
     */
    function get_feedback_summary()
    {
        return $this->get_feedback_display_configuration()->get_feedback_summary();
    }

    /**
     * @return boolean
     */
    function get_feedback_per_page()
    {
        return $this->get_feedback_display_configuration()->get_feedback_per_page();
    }

    /**
     * @return boolean
     */
    function display_numeric_feedback()
    {
        return $this->get_feedback_display_configuration()->display_numeric_feedback();
    }

    /**
     * @return boolean
     */
    function display_textual_feedback()
    {
        return $this->get_feedback_display_configuration()->display_textual_feedback();
    }

    function register_parameters()
    {
        foreach ($this->get_parent()->get_assessment_parameters() as $parameter)
        {
            $this->set_parameter($parameter, Request :: get($parameter));
        }
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