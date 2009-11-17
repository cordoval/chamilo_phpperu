<?php
/**
 * $Id: assessment_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment
 */
require_once dirname(__FILE__) . '/assessment_builder_component.class.php';
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
        
        $this->parse_input_from_table();
    }

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE_CLO :
                $component = AssessmentBuilderComponent :: factory('Browser', $this);
                break;
            case AssessmentBuilder :: ACTION_MERGE_ASSESSMENT :
                $component = AssessmentBuilderComponent :: factory('AssessmentMerger', $this);
                break;
            case AssessmentBuilder :: ACTION_SELECT_QUESTIONS :
                $component = AssessmentBuilderComponent :: factory('QuestionSelecter', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }

    function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $selected_ids = $_POST[ObjectBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($_POST['action'])
            {
                case self :: PARAM_ADD_SELECTED_QUESTIONS :
                    $this->set_action(self :: ACTION_SELECT_QUESTIONS);
                    Request :: set_get(self :: PARAM_QUESTION_ID, $selected_ids);
                    break;
            }
        }
    }
}

?>