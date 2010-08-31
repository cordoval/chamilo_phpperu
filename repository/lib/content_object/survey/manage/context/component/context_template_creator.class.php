<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/forms/context_template_form.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_template.class.php';

class SurveyContextManagerContextTemplateCreatorComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
               
        $context_template = new SurveyContextTemplate();
    
        $form = new SurveyContextTemplateForm(SurveyContextTemplateForm :: TYPE_CREATE, $this->get_url(), $context_template,  $this->get_user(), $this);
        
        if ($form->validate())
        {
            $success = $form->create_context_template();
            if ($success)
            {
                $context_template = $form->get_context_template();
                $this->redirect(Translation :: get('SurveyContextTemplateCreated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_TEMPLATE, SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID => $context_template->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyContextTemplateNotCreated'), (true), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_TEMPLATE));
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