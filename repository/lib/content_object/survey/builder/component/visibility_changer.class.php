<?php


class SurveyBuilderVisibilityChangerComponent extends SurveyBuilder
{
    
    const MESSAGE_VISIBILITY_CHANGED = 'VisibilityChanged';
    const MESSAGE_VISIBILITY_NOT_CHANGED = 'VisibilityNotChanged';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $complex_item_id = Request :: get(SurveyBuilder :: PARAM_COMPLEX_QUESTION_ITEM);
        
               
        if ($complex_item_id)
        {
            $complex_item = RepositoryDataManager::get_instance()->retrieve_complex_content_object_item($complex_item_id);
            $page_id =  $complex_item->get_parent ();
                        
            $complex_item->toggle_visibility();
            $succes = $complex_item->update();
            
            $message = $succes ? self :: MESSAGE_VISIBILITY_CHANGED : self :: MESSAGE_VISIBILITY_NOT_CHANGED;
            
            $this->redirect(Translation :: get($message), ! $succes, array(SurveyBuilder :: PARAM_BUILDER_ACTION => SurveyBuilder :: ACTION_CONFIGURE_PAGE, self::PARAM_SURVEY_PAGE_ID => $page_id));
        }
        else
        {
            $this->redirect(Translation :: get('NoQuestionSelected'), true, array(SurveyBuilder :: PARAM_BUILDER_ACTION => SurveyBuilder :: ACTION_CONFIGURE_PAGE, self::PARAM_SURVEY_PAGE_ID => $page_id));
        }
    }
}
?>