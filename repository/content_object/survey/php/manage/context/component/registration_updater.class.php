<?php namespace repository\content_object\survey;
namespace repository\content_object\survey;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;

require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/forms/context_registration_form.class.php';
require_once Path :: get_repository_content_object_path() . 'survey/php/survey_context_registration.class.php';

class SurveyContextManagerRegistrationUpdaterComponent extends SurveyContextManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

    	$trail = BreadcrumbTrail :: get_instance();

        $context_registration_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID);
		$this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID, $context_registration_id);

        $survey_context_registration = SurveyContextDataManager::get_instance()->retrieve_survey_context_registration($context_registration_id);
		
        $context_registration_id = Request :: get(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID);
		$this->set_parameter(SurveyContextManager :: PARAM_CONTEXT_REGISTRATION_ID, $context_registration_id);

        $form = new SurveyContextRegistrationForm(SurveyContextRegistrationForm :: TYPE_EDIT, $this->get_url(), $survey_context_registration,  $this->get_user(), $this);
		
        if ($form->validate())
        {
            $success = $form->update_context_registration();
        	
            if ($success)
            {
                $this->redirect(Translation :: get('ObjectUpdated',array('OBJECT' => Translation :: get('SurveyContext')), Utilities :: COMMON_LIBRARIES), (false), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_REGISTRATION));
            }
            else
            {
                $this->redirect(Translation :: get('ObjectNotUpdated',array('OBJECT' => Translation :: get('SurveyContext')), Utilities :: COMMON_LIBRARIES), (true), array(SurveyContextManager :: PARAM_ACTION => SurveyContextManager :: ACTION_BROWSE_CONTEXT_REGISTRATION));
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