<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_registration.class.php';

class SurveyContextManagerRegistrationDeleterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $context_registration  = SurveyContextDataManager::get_instance()->retrieve_survey_context_registration($id);
               
                if (! $context_registration->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedContextRegistrationNotDeleted';
                }
                else
                {
                    $message = 'SelectedContextRegistrationsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedContextRegistrationDeleted';
                }
                else
                {
                    $message = 'SelectedContextRegistrationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_REGISTRATION));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoContextRegistrationsSelected')));
        }
    }
}
?>