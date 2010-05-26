<?php
/**
 * $Id: assessment_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment
 */
require_once Path :: get_repository_path() . 'lib/complex_builder/assessment/assessment_builder_component.class.php';
require_once dirname(__FILE__) . '/component/assessment_merger/object_browser_table.class.php';

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

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_MERGE_ASSESSMENT :
                $component = $this->create_component('AssessmentMerger');
                break;
            case self :: ACTION_SELECT_QUESTIONS :
                $component = $this->create_component('QuestionSelecter');
                break;
            case ComplexBuilder::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Creator');
                break; 
            case ComplexBuilder::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM : 
            	$component = $this->create_component('Deleter');
            	break;
            case ComplexBuilder::ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM :
            	$component = $this->create_component('Mover');
            	break;
            case ComplexBuilder::ACTION_VIEW_COMPLEX_CONTENT_OBJECT_ITEM :
            	$component = $this->create_component('Viewer');
            	break;
            case ComplexBuilder::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
            	$component = $this->create_component('Updater');
            	break;
            default : 
            	$this->set_action(ComplexBuilder :: ACTION_BROWSE);
            	$component = $this->create_component('Browser');
        }

            $component->run();
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}

//    function parse_input_from_table()
//    {
//        if (isset($_post['action']))
//        {
//            $selected_ids = $_post[objectbrowsertable :: default_name . objecttable :: checkbox_name_suffix];
//            if (empty($selected_ids))
//            {
//                $selected_ids = array();
//            }
//            elseif (! is_array($selected_ids))
//            {
//                $selected_ids = array($selected_ids);
//            }
//            switch ($_post['action'])
//            {
//                case self :: param_add_selected_questions :
//                    $this->set_action(self :: action_select_questions);
//                    request :: set_get(self :: param_question_id, $selected_ids);
//                    break;
//            }
//        }
//    }
}

?>