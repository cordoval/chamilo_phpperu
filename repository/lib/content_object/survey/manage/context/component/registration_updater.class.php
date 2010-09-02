<?php
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/forms/context_registration_form.class.php';

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_registration.class.php';

class SurveyContextManagerRegistrationUpdaterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
 
    	$trail = BreadcrumbTrail :: get_instance();
               
        $context_registration_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID);
		$this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID, $context_registration_id);
        
        $survey_context_registration = SurveyContextDataManager::get_instance()->retrieve_survey_context_registration($context_registration_id);
		
        $context_registration_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID);
		$this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID, $context_registration_id);        
        
        $form = new SurveyContextRegistrationForm(SurveyContextRegistrationForm :: TYPE_EDIT, $this->get_url(), $survey_context_registration,  $this->get_user(), $this);
        
        if ($form->validate())
        {
            $success = $form->update_context_registration();
            if ($success)
            {
                $this->redirect(Translation :: get('SurveyContextUpdated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_REGISTRATION));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyContextNotUpdated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_REGISTRATION));
            	            }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }
}
?>