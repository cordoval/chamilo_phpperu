<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/component/context_viewer.class.php';

class SurveyContextManagerUnsubscribeUserComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(self :: PARAM_CONTEXT_REL_USER_ID);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $context_rel_user_ids = explode('|', $id);
                               
                $context_id = $context_rel_user_ids[0];
                
                if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_USER_RIGHT, $context_id, SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
                {
                    $context_rel_user = SurveyContextDataManager :: get_instance()->retrieve_survey_context_rel_user($context_rel_user_ids[0], $context_rel_user_ids[1]);
                    if (! $context_rel_user->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
                        
                    //                    Event :: trigger('delete', 'context', array('target_context_id' => $context->get_id(), 'action_user_id' => $user->get_id()));
                    }
                }
            
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedSurveyContextRelUserNotDeleted';
                }
                else
                {
                    $message = 'SelectedSurveyContextRelUsersNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedSurveyContextRelUserDeleted';
                }
                else
                {
                    $message = 'SelectedSurveyContextRelUsersDeleted';
                }
            }
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => $context_rel_user_ids[0], DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_USERS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSurveyContextRelUserSelected')));
        }
    }
}
?>