<?php

require_once Path :: get_repository_path() . '/lib/content_object/survey/survey_context_template_rel_page.class.php';

class SurveyBuilderPageUnsubscriberComponent extends SurveyBuilder
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $ids = Request :: get(SurveyBuilder :: PARAM_TEMPLATE_REL_PAGE_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
                         
            foreach ($ids as $id)
            {
                $templaterelpage_ids = explode('|', $id);
                           
                $conditions = array();
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_PAGE_ID, $templaterelpage_ids[2]);
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $templaterelpage_ids[0]);
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $templaterelpage_ids[1]);
                $condition = new AndCondition($conditions);
                               
                $templaterelpage = SurveyContextDataManager::get_instance()->retrieve_template_rel_pages($condition)->next_result();
                               
                if (! isset($templaterelpage))
                {
                    continue;
                }
                else
                {
               
                    if (! $templaterelpage->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
//                        Events :: trigger_event('unsubscribe_user', 'category', array('target_category_id' => $categoryreluser->get_category_id(), 'target_user_id' => $categoryreluser->get_user_id(), 'action_user_id' => $user->get_id()));
                    }
                }
            
            }
                        
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedSurveyContextTemplateRelPageNotDeleted';
                }
                else
                {
                    $message = 'SelectedSurveyContextTemplateRelPagesNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedSurveyContextTemplateRelPageDeleted';
                }
                else
                {
                    $message = 'SelectedSurveyContextTemplateRelPagesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(SurveyBuilder :: PARAM_BUILDER_ACTION => SurveyBuilder :: ACTION_VIEW_CONTEXT, SurveyBuilder :: PARAM_TEMPLATE_ID => $templaterelpage_ids[1]));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSurveyContextTemplateRelPageSelected')));
        
        }
    }
}
?>