<?php
/**
 * $Id: survey_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey
 */

/**
 * This tool allows a user to publish surveys in his or her course.
 */
class SurveyDisplay extends ComplexDisplay
{
    const ACTION_VIEW_SURVEY = 'survey_viewer';
    
    const DEFAULT_ACTION = self :: ACTION_VIEW_SURVEY;

//    function save_answer($complex_question_id, $answer)
//    {
//        return $this->get_parent()->save_answer($complex_question_id, $answer);
//    }
//
//    function finish_survey($percent)
//    {
//        return $this->get_parent()->finish_survey($percent);
//    }
//
//    function get_go_back_url()
//    {
//        return $this->get_parent()->get_go_back_url();
//    }
//
//    function parse($value)
//    {
//        return $this->get_parent()->parse($value);
//    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }
	
//    function get_context_template_id(){
//    	 return $this->get_parent()->get_context_template_id();
//    }
    
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