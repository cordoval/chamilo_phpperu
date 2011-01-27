<?php
namespace repository\content_object\survey;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Utilities;

require_once Path :: get_repository_content_object_path() . 'survey/php/survey_context_registration.class.php';

class SurveyContextManagerContextDeleterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
     	$context_registration_id = Request :: get(self :: PARAM_CONTEXT_REGISTRATION_ID);
    	   	
        $ids = $_GET[self :: PARAM_CONTEXT_ID];
        $failures = 0;
       
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($id);
                
                if (! $context->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures > 0)
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
            
            $this->redirect(Translation :: get($message,array('OBJECT' => Translation::get('SelectedContext'),'OBJECTS' => Translation::get('SelectedContexts')),Utilities::COMMON_LIBRARIES), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoContextsSelected')));
        }
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_CONTEXT_REGISTRATION_ID);
    }
}
?>