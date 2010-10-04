<?php

require_once Path::get_repository_path () . 'lib/content_object/survey/builder/forms/subscribe_context_template_form.class.php';


class SurveyBuilderSubscribeContextTemplateComponent extends SurveyBuilder
{
    
    private $survey_id;

    function run()
    {
        
        $survey_id = Request :: get(self :: PARAM_SURVEY_ID);
        $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id, Survey :: get_type_name());
                
        $form = new SubscribeContextTemplateForm(SubscribeContextTemplateForm :: TYPE_CREATE, $survey, $this->get_url(array(self :: PARAM_SURVEY_ID => $survey_id)), $this->get_user());
        
        if ($form->validate())
        {
            $form->subscribe_context_template();
            $this->redirect(Translation :: get('ContextTemplateSubscribed'), (false), array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE));
        
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