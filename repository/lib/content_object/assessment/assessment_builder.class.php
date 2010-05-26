<?php
/**
 * $Id: assessment_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment
 */
require_once Path :: get_repository_path() . 'lib/complex_builder/assessment/assessment_builder_component.class.php';
require_once dirname(__FILE__) . '/builder/assessment_merger/object_browser_table.class.php';

class AssessmentBuilder extends ComplexBuilder
{
    const ACTION_MERGE_ASSESSMENT = 'merge_assessment';
    const ACTION_SELECT_QUESTIONS = 'select_questions';
    const PARAM_ADD_SELECTED_QUESTIONS = 'add_selected_questions';
    const PARAM_QUESTION_ID = 'question';
    const PARAM_ASSESSMENT_ID = 'assessment';

    function AssessmentBuilder($parent)
    {
        parent :: __construct($parent);
        
        //$this->parse_input_from_table();
    }

//    function run()
//    {
//        $action = $this->get_action();
//        
//        switch ($action)
//        {
//            case ComplexBuilder :: ACTION_BROWSE_CONTENT_OBJECT :
//                $component = AssessmentBuilderComponent :: factory('Browser', $this);
//                break;
//            case AssessmentBuilder :: ACTION_MERGE_ASSESSMENT :
//                $component = AssessmentBuilderComponent :: factory('AssessmentMerger', $this);
//                break;
//            case AssessmentBuilder :: ACTION_SELECT_QUESTIONS :
//                $component = AssessmentBuilderComponent :: factory('QuestionSelecter', $this);
//                break;
//        }
//        
//        if (! $component)
//            parent :: run();
//        else
//            $component->run();
//    }

//    FUNCTION PARSE_INPUT_FROM_TABLE()
//    {
//        IF (ISSET($_POST['ACTION']))
//        {
//            $SELECTED_IDS = $_POST[OBJECTBROWSERTABLE :: DEFAULT_NAME . OBJECTTABLE :: CHECKBOX_NAME_SUFFIX];
//            IF (EMPTY($SELECTED_IDS))
//            {
//                $SELECTED_IDS = ARRAY();
//            }
//            ELSEIF (! IS_ARRAY($SELECTED_IDS))
//            {
//                $SELECTED_IDS = ARRAY($SELECTED_IDS);
//            }
//            SWITCH ($_POST['ACTION'])
//            {
//                CASE SELF :: PARAM_ADD_SELECTED_QUESTIONS :
//                    $THIS->SET_ACTION(SELF :: ACTION_SELECT_QUESTIONS);
//                    REQUEST :: SET_GET(SELF :: PARAM_QUESTION_ID, $SELECTED_IDS);
//                    BREAK;
//            }
//        }
//    }
}

?>