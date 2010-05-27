<?php
/**
 * $Id: question_selecter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component
 */
require_once Path :: get_repository_path() . '/lib/content_object/assessment/assessment.class.php';

class AssessmentBuilderQuestionSelecterComponent extends AssessmentBuilder
{

    function run()
    {
        $assessment_id = Request :: get(AssessmentBuilder :: PARAM_ASSESSMENT_ID);
        if ($assessment_id)
        {
            $clois = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $assessment_id, ComplexContentObjectItem :: get_table_name()));
            while ($cloi = $clois->next_result())
            {
                $question_ids[] = $cloi->get_ref();
            }
        }
        else
        {
            $question_ids = Request :: get(AssessmentBuilder :: PARAM_QUESTION_ID);
            if (! is_array($question_ids))
                $question_ids = array($question_ids);
        }
        
        if (count($question_ids) == 0)
        {
            $this->display_header(new BreadcrumbTrail());
            $this->display_error_message(Translation :: get('NoQuestionsSelected'));
            $this->display_footer();
        }
        
        $succes = true;
        
        $parent = $this->get_root_content_object()->get_id();
        
        foreach ($question_ids as $question_id)
        {
            $question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_id);
            $cloi = ComplexContentObjectItem :: factory($question->get_type());
            $cloi->set_parent($parent);
            $cloi->set_ref($question_id);
            $cloi->set_user_id($this->get_user_id());
            $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($parent));
            $succes &= $cloi->create();
        }
        
        $message = $succes ? Translation :: get('QuestionsAdded') : Translation :: get('QuestionsNotAdded');
        
        $this->redirect($message, ! $succes, array(AssessmentBuilder :: PARAM_BUILDER_ACTION => AssessmentBuilder :: ACTION_BROWSE));
    }

}

?>