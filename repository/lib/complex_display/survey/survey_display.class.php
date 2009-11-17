<?php
/**
 * $Id: survey_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey
 */

require_once dirname(__FILE__) . '/survey_display_component.class.php';
/**
 * This tool allows a user to publish surveys in his or her course.
 */
class SurveyDisplay extends ComplexDisplay
{
    const ACTION_VIEW_SURVEY = 'view';
    const ACTION_VIEW_SURVEY_RESULT = 'view_result';

    /**
     * Inherited.
     */
    function run()
    {
        $component = parent :: run();
        
        if (! $component)
        {
            $action = $this->get_action();
            
            switch ($action)
            {
                case self :: ACTION_VIEW_SURVEY :
                    $component = SurveyDisplayComponent :: factory('SurveyViewer', $this);
                    break;
                case self :: ACTION_VIEW_SURVEY_RESULT :
                    $component = SurveyDisplayComponent :: factory('SurveyResultViewer', $this);
                    break;
                default :
                    $component = SurveyDisplayComponent :: factory('SurveyViewer', $this);
            }
        }
        
        $component->run();
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