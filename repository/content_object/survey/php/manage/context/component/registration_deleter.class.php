<?php 
namespace repository\content_object\survey;

use common\libraries\Translation;
use common\libraries\Path;

require_once Path :: get_repository_content_object_path() . 'survey/php/survey_context_registration.class.php';

class SurveyContextManagerRegistrationDeleterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[self :: PARAM_CONTEXT_REGISTRATION_ID];
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
                    $message = 'ObjectNotDeleted';
                }
                else
                {
                    $message = 'ObjectsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeleted';
                }
                else
                {
                    $message = 'ObjectsDeleted';
                }
            }

            $this->redirect(Translation :: get($message,array('OBJECT' => Translation :: get('SelectedContextRegistration'), 'OBJECTS' => Translation :: get('SelectedContextRegistrations'))), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_REGISTRATION));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoContextRegistrationsSelected')));
        }
    }
}
?>