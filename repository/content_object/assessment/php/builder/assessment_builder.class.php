<?php
namespace repository\content_object\assessment;

use common\libraries\Request;
use repository\ComplexBuilder;

/**
 * $Id: assessment_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment
 */
require_once dirname(__FILE__) . '/component/assessment_merger/object_browser_table.class.php';

class AssessmentBuilder extends ComplexBuilder
{
    const ACTION_MERGE_ASSESSMENT = 'assessment_merger';
    const ACTION_SELECT_QUESTIONS = 'question_selecter';

    const PARAM_ADD_SELECTED_QUESTIONS = 'add_selected_questions';
    const PARAM_QUESTION_ID = 'question';
    const PARAM_ASSESSMENT_ID = 'assessment';

    function __construct($parent)
    {
        parent :: __construct($parent);
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
        return self :: PARAM_BUILDER_ACTION;
    }
}

?>