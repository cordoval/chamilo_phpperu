<?php

require_once Path :: get_application_path() . 'lib/survey/forms/subscribe_group_form.class.php';
require_once Path :: get_application_path() . 'lib/survey/survey_manager/component/participant_browser.class.php';

class SurveyManagerSubscribeGroupComponent extends SurveyManager
{
   
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
      $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_INVITE, $publication_id, SurveyRights :: TYPE_PUBLICATION))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $publication = SurveyDataManager::get_instance()->retrieve_survey_publication($publication_id);
        
        $form = new SurveySubscribeGroupForm($publication, $this->get_url(array(self :: PARAM_PUBLICATION_ID => Request :: get(self :: PARAM_PUBLICATION_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_group_rights();
            if ($success)
            {
                $this->redirect(Translation :: get('SurveyGroupsSubscribed'), (false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PARTICIPANTS, self :: PARAM_PUBLICATION_ID => $publication_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyManagerParticipantBrowserComponent :: TAB_INVITEES));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyGroupsNotSubscribed'), (true), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PARTICIPANTS, self :: PARAM_PUBLICATION_ID => $publication_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyManagerParticipantBrowserComponent :: TAB_INVITEES));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveyPublications')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PARTICIPANTS, self :: PARAM_PUBLICATION_ID => Request :: get(self :: PARAM_PUBLICATION_ID))), Translation :: get('BrowseSurveyParticipants')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }

}
?>