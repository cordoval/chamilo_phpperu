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

    function get_go_back_url()
    {
        return $this->get_parent()->get_go_back_url();
    }
}

?>
