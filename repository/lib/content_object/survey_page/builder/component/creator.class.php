<?php

//require_once dirname(__FILE__) . '/../survey_page_builder_component.class.php';
//require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
//require_once Path :: get_repository_path() . '/lib/content_object/survey_page/survey_page.class.php';

class SurveyPageBuilderCreatorComponent extends SurveyPageBuilder
{
	function run()
	{
		
		$creator = ComplexBuilderComponent ::factory(ComplexBuilderComponent::CREATOR_COMPONENT, $this);
		
		$creator->run();
	}
	
//    function run()
//    {
//        $survey_page_id = Request :: get(SurveyPageBuilder :: PARAM_SURVEY_PAGE_ID);
//        if ($survey_page_id)
//        {
//            $clois = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_page_id, ComplexContentObjectItem :: get_table_name()));
//            while ($cloi = $clois->next_result())
//            {
//                $question_ids[] = $cloi->get_ref();
//            }
//        }
//        else
//        {
//            $question_ids = Request :: get(SurveyPageBuilder :: PARAM_QUESTION_ID);
//            if (! is_array($question_ids))
//                $question_ids = array($question_ids);
//        }
//        
//        if (count($question_ids) == 0)
//        {
//            $this->display_header(BreadcrumbTrail :: get_instance());
//            $this->display_error_message(Translation :: get('NoQuestionsSelected'));
//            $this->display_footer();
//        }
//        
//        $succes = true;
//        
//        $parent = $this->get_root_lo()->get_id();
//        
//        foreach ($question_ids as $question_id)
//        {
//            $question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_id);
//            $cloi = ComplexContentObjectItem :: factory($question->get_type());
//            $cloi->set_parent($parent);
//            $cloi->set_ref($question_id);
//            $cloi->set_user_id($this->get_user_id());
//            $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($parent));
//            $succes &= $cloi->create();
//        }
//        
//        $message = $succes ? Translation :: get('QuestionsAdded') : Translation :: get('QuestionsNotAdded');
//        
//        $this->redirect($message, ! $succes, array(SurveyPageBuilder :: PARAM_BUILDER_ACTION => SurveyPageBuilder :: ACTION_BROWSE_CLO, SurveyPageBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), 'publish' => Request :: get('publish')));
//    }

}

?>