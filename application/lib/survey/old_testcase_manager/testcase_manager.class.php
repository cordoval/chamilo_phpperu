<?php
require_once Path :: get_application_path() . 'lib/survey/testcase_manager/component/publication_browser/publication_browser_table.class.php';

class TestcaseManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    const PARAM_SURVEY_PUBLICATION = 'survey_publication';
    const PARAM_SURVEY_PARTICIPANT = 'survey_participant';
    const PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS = 'delete_publications';
    
    const ACTION_BROWSE_SURVEY_PUBLICATIONS = 'browser';
    const ACTION_CREATE_SURVEY_PUBLICATION = 'creator';
    const ACTION_DELETE_SURVEY_PUBLICATION = 'deleter';
    const ACTION_UPDATE_SURVEY_PUBLICATION = 'updater';
    const ACTION_BROWSE_SURVEY_PARTICIPANTS = 'participant_browser';
    const ACTION_BROWSE_SURVEY_EXCLUDED_USERS = 'user_browser';
    const ACTION_CHANGE_TEST_TO_PRODUCTION = 'changer';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_SURVEY_PUBLICATIONS;
    
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
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_CREATE_SURVEY_PUBLICATION, SurveyManager :: PARAM_TESTCASE => '1'));
    }

    function get_update_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_EDIT_SURVEY_PUBLICATION, SurveyManager :: PARAM_TESTCASE => '1', SurveyManager :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_delete_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_DELETE_SURVEY_PUBLICATION, SurveyManager :: PARAM_TESTCASE => '1', SurveyManager :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_reporting_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_REPORTING, SurveyManager :: PARAM_TESTCASE => '1', SurveyManager :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_build_survey_url($survey_publication)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BUILD_SURVEY, SurveyManager :: PARAM_TESTCASE => '1', SurveyManager :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_browse_survey_publication_url()
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
    }

    function get_browse_survey_participants_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PARTICIPANTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_browse_survey_excluded_users_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_EXCLUDED_USERS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_survey_publication_viewer_url($survey_participant)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_VIEW_SURVEY_PUBLICATION, SurveyManager :: PARAM_TESTCASE => '1', SurveyManager :: PARAM_SURVEY_PARTICIPANT => $survey_participant->get_id()));
    }

    function get_change_test_to_production_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_TEST_TO_PRODUCTION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    private function parse_input_from_table()
    {
        
        if (isset($_POST['action']))
        {
            
            if (isset($_POST[TestcaseSurveyPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[TestcaseSurveyPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            }
            
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
                
                case self :: PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS :
                    $this->set_test_case_action(self :: ACTION_DELETE_SURVEY_PUBLICATION);
                    $_GET[self :: PARAM_SURVEY_PUBLICATION] = $selected_ids;
                    break;
            
            }
        }
    }

    private function set_test_case_action($action)
    {
        $this->set_parameter(self :: PARAM_ACTION, $action);
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}
?>