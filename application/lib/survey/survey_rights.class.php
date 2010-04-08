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

    function get_available_rights()
    {
        $reflect = new ReflectionClass('SurveyRights');
        return $reflect->getConstants();
    }

    function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, SurveyManager :: APPLICATION_NAME);
    }

    function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier(SurveyManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier(SurveyManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_root_id()
    {
        return RightsUtilities :: get_root_id(SurveyManager :: APPLICATION_NAME);
    }

    function get_root()
    {
        return RightsUtilities :: get_root(SurveyManager :: APPLICATION_NAME);
    }
}
?>