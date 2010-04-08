<?php
/**
 * $Id: assessment_display_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment
 */
/**
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class AssessmentDisplayComponent extends ComplexDisplayComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Assessment', $component_name, $builder);
    }

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
}

?>