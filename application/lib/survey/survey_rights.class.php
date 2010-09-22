<?php
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_application_path() . 'lib/survey/survey_manager/survey_manager.class.php';

class SurveyRights
{
    const RIGHT_VIEW = 1;
    const VIEW_RIGHT_NAME = 'view';
    const RIGHT_INVITE = 2;
    const INVITE_RIGHT_NAME = 'invite';
    const RIGHT_EDIT = 3;
    const EDIT_RIGHT_NAME = 'edit';
    const RIGHT_DELETE = 4;
    const DELETE_RIGHT_NAME = 'delete';
    const RIGHT_PUBLISH = 5;
    const PUBLISH_RIGHT_NAME = 'publish';
    const RIGHT_REPORTING = 6;
    const REPORTING_RIGHT_NAME = 'reporting';
    const RIGHT_MAIL = 7;
    const MAIL_RIGHT_NAME = 'mail';
    const RIGHT_EXPORT_RESULT = 8;
    const EXPORT_RESULT_RIGHT_NAME = 'export_result';
    const RIGHT_PARTICIPATE = 9;
    const PARTICIPATE_RIGHT_NAME = 'participate';
    
    const LOCATION_BROWSER = 1;
    const LOCATION_REPORTING = 2;
    
    const TREE_TYPE_SURVEY = 1;
    
    const TYPE_SURVEY_COMPONENT = 1;
    const TYPE_PUBLICATION = 2;

    static function get_available_rights_for_publications()
    {
        return array(self :: PARTICIPATE_RIGHT_NAME => self :: RIGHT_PARTICIPATE, self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: INVITE_RIGHT_NAME => self :: RIGHT_INVITE, self :: EDIT_RIGHT_NAME => self :: RIGHT_EDIT, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE, self :: REPORTING_RIGHT_NAME => self :: RIGHT_REPORTING, self :: MAIL_RIGHT_NAME => self :: RIGHT_MAIL, self :: EXPORT_RESULT_RIGHT_NAME => self :: RIGHT_EXPORT_RESULT);
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

    static function is_allowed_in_surveys_subtree($right,$identifier, $type, $user_id = null)
    {
        return RightsUtilities :: is_allowed($right, $identifier, $type, SurveyManager :: APPLICATION_NAME, $user_id, 0, self :: TREE_TYPE_SURVEY);
    }

    static function create_surveys_subtree_root_location()
    {
        return RightsUtilities :: create_location('surveys_tree', SurveyManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_SURVEY);
    }

    static function get_allowed_users($right, $identifier, $type)
    {
        return RightsUtilities :: get_allowed_users($right, $identifier, $type, SurveyManager :: APPLICATION_NAME);
    }

}
?>