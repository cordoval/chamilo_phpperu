<?php
/**
 * $Id: survey_display_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey
 */
/**
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class SurveyDisplayComponent extends ComplexDisplayComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Survey', $component_name, $builder);
    }

    function save_answer($complex_question_id, $answer)
    {
        return $this->get_parent()->save_answer($complex_question_id, $answer);
    }

    //    function get_total_questions(){
    //    	return $this->get_parent()->get_total_questions();
    //    }
    

    function finish_survey($percent)
    {
        return $this->get_parent()->finish_survey($percent);
    }

    function get_go_back_url()
    {
        return $this->get_parent()->get_go_back_url();
    }

    function parse($value)
    {
        return $this->get_parent()->parse($value);
    }

    function get_template_id()
    {
        return $this->get_parent()->get_template_id();
    }

    function set_template_id($template_id)
    {
        return $this->get_parent()->set_template_id($template_id);
    }

}

?>