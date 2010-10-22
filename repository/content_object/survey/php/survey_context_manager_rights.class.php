<?php 
namespace repository\content_object\survey;

use common\libraries\Path;

require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/survey_context_manager.class.php';

class SurveyContextManagerRights
{
    const RIGHT_VIEW = 1;
    const VIEW_RIGHT_NAME = 'view';
    const RIGHT_ADD = 2;
    const ADD_RIGHT_NAME = 'add';
    const RIGHT_EDIT = 3;
    const EDIT_RIGHT_NAME = 'edit';
    const RIGHT_DELETE = 4;
    const DELETE_RIGHT_NAME = 'delete';
    
    const SUBSCRIBE_GROUP_RIGHT = 7;
    const SUBSCRIBE_GROUP_NAME = 'subscribe_group';
    
    const SUBSCRIBE_USER_RIGHT = 8;
    const SUBSCRIBE_USER_NAME = 'subscribe_user';
    
    const LOCATION_CONTEXT_REGISTRATION = 1;
    const LOCATION_CONTEXT_TEMPLATE = 2;
    
    const TREE_TYPE_SURVEY_CONTEXT_MANAGER = 0;
    
    const TYPE_COMPONENT = 1;
    const TYPE_CONTEXT_TEMPLATE = 2;
    const TYPE_CONTEXT_REGISTRATION = 3;

    static function get_available_rights_for_components()
    {
        return array(self :: ADD_RIGHT_NAME => self :: RIGHT_ADD, self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: EDIT_RIGHT_NAME => self :: RIGHT_EDIT, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE);
    }

    static function get_administration_locations()
    {
        return array(self :: LOCATION_CONTEXT_REGISTRATION, self :: LOCATION_CONTEXT_TEMPLATE);
    }

    static function get_available_rights_for_context_registrations()
    {
        return array(self :: ADD_RIGHT_NAME => self :: RIGHT_ADD, self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: EDIT_RIGHT_NAME => self :: RIGHT_EDIT, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE, self :: SUBSCRIBE_GROUP_NAME => self :: SUBSCRIBE_GROUP_RIGHT, self :: SUBSCRIBE_USER_NAME => self :: SUBSCRIBE_USER_RIGHT);
    }

    static function get_available_rights_for_context_templates()
    {
        return array(self :: ADD_RIGHT_NAME => self :: RIGHT_ADD, self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: EDIT_RIGHT_NAME => self :: RIGHT_EDIT, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE);
    }

    static function create_location_in_survey_context_manager_subtree($name, $identifier, $parent, $type, $return_location = false)
    {
        return RightsUtilities :: create_location($name, SurveyContextManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_SURVEY_CONTEXT_MANAGER, $return_location);
    }

    static function get_survey_context_manager_subtree_root()
    {
        return RightsUtilities :: get_root(SurveyContextManager :: APPLICATION_NAME, self :: TREE_TYPE_SURVEY_CONTEXT_MANAGER, 0);
    }

    static function get_survey_context_manager_subtree_root_id()
    {
        return RightsUtilities :: get_root_id(SurveyContextManager :: APPLICATION_NAME, self :: TREE_TYPE_SURVEY_CONTEXT_MANAGER, 0);
    }

    static function get_location_id_by_identifier_from_survey_context_manager_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_id_by_identifier(SurveyContextManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_SURVEY_CONTEXT_MANAGER);
    }

    static function get_location_by_identifier_from_survey_context_manager_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_by_identifier(SurveyContextManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_SURVEY_CONTEXT_MANAGER);
    }

    static function is_allowed_in_survey_context_manager_subtree($right, $identifier, $type, $user_id = null)
    {
        return RightsUtilities :: is_allowed($right, $identifier, $type, SurveyContextManager :: APPLICATION_NAME, $user_id, 0, self :: TREE_TYPE_SURVEY_CONTEXT_MANAGER);
    }

    static function create_survey_context_manager_subtree_root_location()
    {
        return RightsUtilities :: create_location('survey_context_manager_tree', SurveyContextManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_SURVEY_CONTEXT_MANAGER);
    }

    static function get_allowed_users($right, $identifier, $type)
    {
        return RightsUtilities :: get_allowed_users($right, $identifier, $type, SurveyContextManager :: APPLICATION_NAME);
    }

}
?>