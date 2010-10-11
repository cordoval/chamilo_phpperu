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