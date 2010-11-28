<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Translation;

require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/forms/context_form.class.php';
require_once Path :: get_repository_content_object_path() . 'survey/php/survey_context.class.php';

class SurveyContextManagerContextCreatorComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
 
    	$trail = BreadcrumbTrail :: get_instance();
               
        $context_registration_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID);
		$this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID, $context_registration_id);
        
        $context_registration = SurveyContextDataManager::get_instance()->retrieve_survey_context_registration($context_registration_id);
    	
        $survey_context = SurveyContext :: factory($context_registration->get_type());
        $survey_context->set_context_registration_id($context_registration_id);
        
        $form = new SurveyContextForm(SurveyContextForm :: TYPE_CREATE, $this->get_url(), $survey_context,  $this->get_user(), $this);
        
        if ($form->validate())
        {
            $success = $form->create_survey_context();
            if ($success)
            {
                $this->redirect(Translation :: get('SurveyContextCreated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_REGISTRATION, SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id));
            }
            else
            {
                $this->redirect(Translation :: get('SurveyContextNotCreated'), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_VIEW_CONTEXT_REGISTRATION, SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id));
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