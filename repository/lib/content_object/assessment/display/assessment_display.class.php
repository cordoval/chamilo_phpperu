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
    const ACTION_VIEW_ASSESSMENT = 'view';
    const ACTION_VIEW_ASSESSMENT_RESULT = 'view_result';

    /**
     * Inherited.
     */
    function run()
    {     
        if (! $component)
        {
            $action = $this->get_action();
            
            switch ($action)
            {
                case self :: ACTION_VIEW_ASSESSMENT :
                    $component = $this->create_component('AssessmentViewer');
                    break;
                case self :: ACTION_VIEW_ASSESSMENT_RESULT :
                    $component = $this->create_component('AssessmentResultViewer');
                    break;
                default :
                    $component = $this->create_component('AssessmentViewer');
            }
        }
        
        $component->run();
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
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>