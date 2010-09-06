<?php
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_application_path() . 'lib/survey/survey_manager/survey_manager.class.php';

class SurveyRights
{
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';
    const PUBLISH_RIGHT = '5';
    const MOVE_RIGHT = '6';
    const MAIL_RIGHT = '7';
    
    const LOCATION_BROWSER = 1;
    const LOCATION_REPORTING = 2;
    
    const TREE_TYPE_SURVEY = 1;
    
    const TYPE_SURVEY_COMPONENT = 1;
    const TYPE_PUBLICATION = 2;
    

    static function get_available_rights_for_publications()
    {
        return array('View' => self :: VIEW_RIGHT);
    }
   

    static function create_location_in_surveys_subtree($name, $identifier, $parent, $type)
    {
        return RightsUtilities :: create_location($name, SurveyManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_SURVEY);
    }

    static function get_surveys_subtree_root()
    {
        return RightsUtilities :: get_root(SurveyManager :: APPLICATION_NAME, self :: TREE_TYPE_SURVEY, 0);
    }

    static function get_surveys_subtree_root_id()
    {
        return RightsUtilities :: get_root_id(SurveyManager :: APPLICATION_NAME, self :: TREE_TYPE_SURVEY, 0);
    }

    static function get_location_id_by_identifier_from_surveys_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_id_by_identifier(SurveyManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_SURVEY);
    }

    static function get_location_by_identifier_from_surveys_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_by_identifier(SurveyManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_SURVEY);
    }

    static function is_allowed_in_surveys_subtree($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, SurveyManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_SURVEY);
    }

    static function create_surveys_subtree_root_location()
    {
        return RightsUtilities :: create_location('surveys_tree', SurveyManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_SURVEY);
    }
    	
}
?>