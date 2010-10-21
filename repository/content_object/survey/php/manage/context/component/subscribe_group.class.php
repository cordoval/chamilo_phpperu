<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/forms/context_subscribe_group_form.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/component/context_viewer.class.php';

class SurveyContextManagerSubscribeGroupComponent extends SurveyContextManager
{
   
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $context_id = Request :: get(self :: PARAM_CONTEXT_ID);
        
//        if (! SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_GROUP_RIGHT, $context_id, SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
//        {
//            $this->display_header();
//            $this->display_error_message(Translation :: get('NotAllowed'));
//            $this->display_footer();
//            exit();
//        }
        
        $context = SurveyContextDataManager::get_instance()->retrieve_survey_context_by_id($context_id);
        
        $form = new SurveyContextSubscribeGroupForm($context, $this->get_url(array(self :: PARAM_CONTEXT_ID => Request :: get(self :: PARAM_CONTEXT_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_context_rel_users();
            if ($success)
            {
                $this->redirect(Translation :: get('SurveyContextRelGroupsCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => $context_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_GROUPS));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyContextRelGroupsNotCreated'), (true), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => $context_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_GROUPS));
            }
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

 function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_REGISTRATION)), Translation :: get('BrowseContextRegistrations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => Request :: get(self :: PARAM_CONTEXT_REGISTRATION_ID))), Translation :: get('ViewContextRegistration')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_CONTEXT_ID => Request :: get(self :: PARAM_CONTEXT_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyContextManagerContextViewerComponent :: TAB_GROUPS)), Translation :: get('BrowseSurveyContexts')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_REGISTRATION_ID, self :: PARAM_CONTEXT_ID);
    }

}
?>