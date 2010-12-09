<?php
namespace application\survey;

use common\libraries\Request;

use common\extensions\invitation_manager\InvitationManager;
use common\extensions\invitation_manager\InvitationSupport;

class SurveyManagerInviterComponent extends SurveyManager implements InvitationSupport
{
	
    function run()
    {
        $invitation_manager = new InvitationManager($this);
        $invitation_manager->run();
    }

    function get_url_parameters()
    {
    	$publication_id = Request:: get(SurveyManager::PARAM_PUBLICATION_ID);
    	$publication = SurveyDataManager::get_instance()->retrieve_survey_publication($publication_id);
    	$survey_id = $publication->get_content_object_id();
    	
    	return array(SurveyManager::PARAM_APPLICATION => SurveyManager::APPLICATION_NAME, 
    	SurveyManager::PARAM_ACTION => SurveyManager::ACTION_TAKE,
    	SurveyManager :: PARAM_PUBLICATION_ID => $publication_id,
    	SurveyManager :: PARAM_SURVEY_ID => $survey_id);
    }

    function process_existing_users(array $user_ids = array())
    {
    
    }

    function get_location_rights_ids()
    {
    
    }

}
?>