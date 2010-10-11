<?php
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/forms/context_form.class.php';

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context.class.php';

class SurveyContextManagerContextUpdaterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
 
    	$trail = BreadcrumbTrail :: get_instance();
               
        $context_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_ID);
		$this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_ID, $context_id);
        
        $survey_context = SurveyContextDataManager::get_instance()->retrieve_survey_context_by_id($context_id);
		
        $context_registration_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID);
		$this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID, $context_registration_id);        
        
        $form = new SurveyContextForm(SurveyContextForm :: TYPE_EDIT, $this->get_url(), $survey_context,  $this->get_user(), $this);
        
        if ($form->validate())
        {
            $success = $form->update_survey_context();
            if ($success)
            {
                $this->redirect(Translation :: get('SurveyContextUpdated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_REGISTRATION, SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyContextNotUpdated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_REGISTRATION, SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id));
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