<?php
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/forms/template_form.class.php';

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_template.class.php';

class SurveyContextManagerTemplateCreatorComponent extends SurveyContextManager
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
    	
        $survey_template = SurveyTemplate :: factory($context_template->get_type());
        
        $form = new SurveyTemplateForm(SurveyTemplateForm :: TYPE_CREATE, $this->get_url(), $survey_template,  $this->get_user(), $this);
        
        if ($form->validate())
        {
            $success = $form->create_survey_template();
            if ($success)
            {
                $this->redirect(Translation :: get('SurveyTemplateCreated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_TEMPLATE, SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyTemplateNotCreated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_TEMPLATE, SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID => $context_template_id));
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