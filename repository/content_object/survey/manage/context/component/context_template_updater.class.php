<?php
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/forms/context_template_form.class.php';

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_template.class.php';

class SurveyContextManagerContextTemplateUpdaterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
 
    	$trail = BreadcrumbTrail :: get_instance();
               
        $context_template_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID);
		$this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID, $context_template_id);
        
        $context_template = SurveyContextDataManager::get_instance()->retrieve_survey_context_template($context_template_id);
		       
        $form = new SurveyContextTemplateForm(SurveyContextTemplateForm :: TYPE_EDIT, $this->get_url(), $context_template,  $this->get_user(), $this);
        
        if ($form->validate())
        {
            $success = $form->update_context_template();
            if ($success)
            {
                $this->redirect(Translation :: get('SurveyContextTemplateUpdated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_TEMPLATE));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyContextTemplateNotUpdated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_TEMPLATE));
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