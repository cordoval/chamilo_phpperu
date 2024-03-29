<?php
namespace application\survey;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\DynamicTabsRenderer;

require_once dirname(__FILE__) . '/browser.class.php';

class SurveyReportingManagerCreatorComponent extends SurveyReportingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        $id = Request :: get(self :: PARAM_REPORTING_TEMPLATE_REGISTRATION_ID);
        
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_ADD_REPORTING_TEMPLATE, $publication_id, SurveyRights :: TYPE_PUBLICATION))
        {
            
            $publication_rel_reporting_template_registration = new SurveyPublicationRelReportingTemplateRegistration();
            $publication_rel_reporting_template_registration->set_publication_id($publication_id);
            $publication_rel_reporting_template_registration->set_reporting_template_registration_id($id);
            $publication_rel_reporting_template_registration->set_owner_id($this->get_user_id());
            
            $form = new SurveyPublicationRelReportingTemplateRegistrationForm($this, SurveyPublicationRelReportingTemplateRegistrationForm :: TYPE_CREATE, $publication_rel_reporting_template_registration, $this->get_url(array(SurveyManager :: PARAM_PUBLICATION_ID => $publication_id, self :: PARAM_REPORTING_TEMPLATE_REGISTRATION_ID => $id)), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->create();
                
                if ($success)
                {
                    $message = 'SelectedReportingTemplateUpdated';
                }
                else
                {
                    $message = 'SelectedReportingTemplateNotUpdated';
                }
                if ($success)
                {
                    
                    $message = 'SelectedReportingTemplateActivated';
                    
                    $tab = SurveyReportingManagerBrowserComponent :: TAB_TEMPLATE_REGISTRATIONS;
                }
                else
                {
                    $message = 'SelectedReportingTemplateNotActivated';
                    
                    $tab = SurveyReportingManagerBrowserComponent :: TAB_PUBLICATION_REL_TEMPLATE_REGISTRATIONS;
                }
                
                $this->redirect(Translation :: get($message), ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE, SurveyManager :: PARAM_PUBLICATION_ID => $publication_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
            
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        
        }
        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
    
    }
}
?>