<?php

require_once Path :: get_application_path() . 'lib/survey/forms/survey_publication_form.class.php';

class SurveyManagerEditorComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication = SurveyDataManager::get_instance()->retrieve_survey_publication(Request :: get(self :: PARAM_PUBLICATION_ID));
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_EDIT, $publication->get_id(), SurveyRights :: TYPE_PUBLICATION))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $form = new SurveyPublicationForm(SurveyPublicationForm :: TYPE_EDIT, $publication, $this->get_user(),$this->get_url(array(self :: PARAM_PUBLICATION_ID => $publication->get_id())),  $publication);
        
        if ($form->validate())
        {
            $success = $form->update_publication();
            $tab = $form->get_publication_type();
            $this->redirect($success ? Translation :: get('SurveyPublicationUpdated') : Translation :: get('SurveyPublicationNotUpdated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE, DynamicTabsRenderer::PARAM_SELECTED_TAB => $tab));
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
        $publication = SurveyDataManager::get_instance()->retrieve_survey_publication(Request :: get(self :: PARAM_PUBLICATION_ID));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE, DynamicTabsRenderer::PARAM_SELECTED_TAB => $publication->get_type())), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }

}
?>