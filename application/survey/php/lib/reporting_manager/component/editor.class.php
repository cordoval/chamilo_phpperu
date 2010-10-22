<?php namespace application\survey;

require_once Path :: get_application_path() . 'lib/survey/reporting_manager/component/browser.class.php';
require_once Path :: get_application_path() . 'lib/survey/forms/publication_rel_reporting_template_form.class.php';

class SurveyReportingManagerEditorComponent extends SurveyReportingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        $id = Request :: get(self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID);
        
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_ADD_REPORTING_TEMPLATE, $publication_id, SurveyRights :: TYPE_PUBLICATION))
        {
            
            $publication_rel_reporting_template_registration = SurveyDataManager :: get_instance()->retrieve_survey_publication_rel_reporting_template_registration_by_id($id);
            
            $form = new SurveyPublicationRelReportingTemplateRegistrationForm($this, SurveyPublicationRelReportingTemplateRegistrationForm :: TYPE_EDIT, $publication_rel_reporting_template_registration, $this->get_url(array(SurveyManager :: PARAM_PUBLICATION_ID => $publication_id, self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID => $id)), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update();
                if ($success)
                {
                    $message = 'SelectedReportingTemplateLevelChanged';
                }
                else
                {
                    $message = 'SelectedReportingTemplateLevelNotChanged';
                }
                
                $this->redirect(Translation :: get($message), ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE, SurveyManager :: PARAM_PUBLICATION_ID => $publication_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => SurveyReportingManagerBrowserComponent :: TAB_PUBLICATION_REL_TEMPLATE_REGISTRATIONS));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        
        }
    
    }
}
?>