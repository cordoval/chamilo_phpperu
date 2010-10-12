<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/forms/context_registration_form.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_registration.class.php';

class SurveyContextManagerRegistrationCreatorComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
               
        $context_registration = new SurveyContextRegistration();
    
        $form = new SurveyContextRegistrationForm(SurveyContextRegistrationForm :: TYPE_CREATE, $this->get_url(), $context_registration,  $this->get_user(), $this);
        
        if ($form->validate())
        {
            $success = $form->create_context_registration();
            if ($success)
            {
                $context_registration = $form->get_context_registration();
                $this->redirect(Translation :: get('SurveyContextRegistrationCreated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_REGISTRATION, SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyContextRegistrationNotCreated'), (true), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_REGISTRATION));
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