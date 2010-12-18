<?php
namespace repository\content_object\survey_page;

use repository\ComplexBuilder;

/**
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyPageBuilder extends ComplexBuilder //implements ComplexMenuSupport
{
    const ACTION_MERGE_SURVEY_PAGE = 'merger';
    const ACTION_SELECT_QUESTIONS = 'question_selecter';

    const PARAM_QUESTION_ID = 'question';
    const PARAM_SURVEY_PAGE_ID = 'survey_page';

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
        return self :: PARAM_BUILDER_ACTION;
    }

}

?>