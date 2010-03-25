<?php
require_once Path :: get_application_path() . 'lib/survey/testcase_manager/component/publication_browser/publication_browser_table.class.php';

class TestcaseManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    const PARAM_SURVEY_PUBLICATION = 'survey_publication';
    const PARAM_SURVEY_PARTICIPANT = 'survey_participant';
    
    const ACTION_BROWSE_SURVEY_PUBLICATIONS = 'browse';
    const ACTION_CREATE_SURVEY_PUBLICATION = 'create';
    const ACTION_DELETE_SURVEY_PUBLICATION = 'delete';
    const ACTION_UPDATE_SURVEY_PUBLICATION = 'update';
    const ACTION_BROWSE_SURVEY_PARTICIPANTS = 'browse_participants';
    const ACTION_VIEW_SURVEY_PUBLICATION = 'view_survey_publication';
	const ACTION_BUILD_SURVEY = 'build';
	const ACTION_CHANGE_TEST_TO_PRODUCTION = 'change_to_production';
	
    function TestcaseManager($survey_manager)
    {
        parent :: __construct($survey_manager);
        $action = Request :: get(self :: PARAM_ACTION);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
        $this->parse_input_from_table();
    
    }

    function run()
    {
        $action = $this->get_parameter(self :: PARAM_ACTION);
        
        switch ($action)
        {
            
            case self :: ACTION_CREATE_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('Creator', $this->get_survey_manager());
                break;
            case self :: ACTION_DELETE_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('Deleter', $this->get_survey_manager());
                break;
            case self :: ACTION_UPDATE_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('Updater', $this->get_survey_manager());
                break;
            case self :: ACTION_BUILD_SURVEY :
                $component = SurveyManagerComponent :: factory('Builder', $this->get_survey_manager());
                break;    
            case self :: ACTION_BROWSE_SURVEY_PUBLICATIONS :
                $component = TestcaseManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_BROWSE_SURVEY_PARTICIPANTS :
                $component = TestcaseManagerComponent :: factory('ParticipantBrowser', $this);
                break;
            case self :: ACTION_VIEW_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('Viewer', $this->get_survey_manager());
                break;
             case self :: ACTION_CHANGE_TEST_TO_PRODUCTION :
                $component = SurveyManagerComponent :: factory('Changer', $this);
                break;    
            default :
                $component = TestcaseManagerComponent :: factory('Browser', $this);
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/survey/testcase_manager/component/';
    }

    function get_survey_manager()
    {
        return $this->get_parent();
    }

    //url creation
    

    function get_create_survey_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_SURVEY_PUBLICATION));
    }

    function get_update_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_delete_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }
	
	function get_build_survey_url($survey_publication) {
		return $this->get_url ( array (self::PARAM_ACTION => self::ACTION_BUILD_SURVEY, self::PARAM_SURVEY_PUBLICATION => $survey_publication->get_id () ) );
	}
    
    function get_browse_survey_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
    }

    function get_browse_survey_participants_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PARTICIPANTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_survey_publication_viewer_url($survey_participant)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PARTICIPANT => $survey_participant->get_id()));
    }
	
     function get_change_test_to_production_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_TEST_TO_PRODUCTION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }
    
    
    private function parse_input_from_table()
    {
        
        if (isset($_POST['action']))
        {
            
            //            if (isset($_POST[InternshipOrganisationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            //            {
            //                $selected_ids = $_POST[InternshipOrganisationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            //            }
            

            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($_POST['action'])
            {
                //                case self :: PARAM_UNSUBSCRIBE_SELECTED :
            //                    $this->set_organisation_action(self :: ACTION_UNSUBSCRIBE_USER_FROM_GROUP);
            //                    $_GET[self :: PARAM_GROUP_REL_STUDENT_ID] = $selected_ids;
            //                    break;
            //                case self :: PARAM_SUBSCRIBE_SELECTED :
            //                    $this->set_group_action(self :: ACTION_SUBSCRIBE_USER_TO_GROUP);
            //                    $_GET[StsManager :: PARAM_USER_ID] = $selected_ids;
            //                    break;
            //                case self :: PARAM_DELETE_SELECTED_ORGANISATIONS :
            //                    $this->set_action(self :: ACTION_DELETE_ORGANISATION);
            //                    $_GET[self :: PARAM_ORGANISATION_ID] = $selected_ids;
            //                    break;
            //                case self :: PARAM_TRUNCATE_SELECTED :
            //                    $this->set_group_action(self :: ACTION_TRUNCATE_GROUP);
            //                    $_GET[self :: PARAM_GROUP_ID] = $selected_ids;
            //                    break;
            

            }
        }
    }
    
//	private function set_action($action) {
//		$this->set_parameter ( self::PARAM_ACTION, $action );
//	}
}

?>