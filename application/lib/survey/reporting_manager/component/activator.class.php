<?php

require_once Path :: get_application_path() . 'lib/survey/reporting_manager/component/browser.class.php';

class SurveyReportingManagerActivatorComponent extends SurveyReportingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        $ids = Request :: get(self :: PARAM_REPORTING_TEMPLATE_REGISTRATION_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_ADD_REPORTING_TEMPLATE, $publication_id, SurveyRights :: TYPE_PUBLICATION))
            {
                foreach ($ids as $id)
                {
                        $publication_rel_reporting_template_registration = new SurveyPublicationRelReportingTemplateRegistration();
                       	$publication_rel_reporting_template_registration->set_publication_id($publication_id);
                       	$publication_rel_reporting_template_registration->set_reporting_template_registration_id($id);
                       	$publication_rel_reporting_template_registration->set_owner_id($this->get_user_id());
                        
                        if (! $publication_rel_reporting_template_registration->create())
                        {
                            $failures ++;
                        }
                }
            }
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedReportingTemplateNotActivated';
                }
                else
                {
                    $message = 'SelectedReportingTemplatesNotActivated';
                }
                $tab = SurveyReportingManagerBrowserComponent :: TAB_TEMPLATE_REGISTRATIONS;
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedReportingTemplateActivated';
                }
                else
                {
                    $message = 'SelectedReportingTemplatesActivated';
                }
                 $tab = SurveyReportingManagerBrowserComponent :: TAB_PUBLICATION_REL_TEMPLATE_REGISTRATIONS;
            }
            
          
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REPORTING_TEMPLATES, SurveyManager :: PARAM_PUBLICATION_ID => $publication_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('SelectedReportingTemplatesSelected')));
        }
    }
}
?>