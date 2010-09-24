<?php
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

class InternshipOrganizerRights
{
    const RIGHT_VIEW = 1;
    const VIEW_RIGHT_NAME = 'view';
    const RIGHT_ADD = 2;
    const ADD_RIGHT_NAME = 'add';
    const RIGHT_EDIT = 3;
    const EDIT_RIGHT_NAME = 'edit';
    const RIGHT_DELETE = 4;
    const DELETE_RIGHT_NAME = 'delete';
    const RIGHT_PUBLISH = 5;
    const PUBLISH_RIGHT_NAME = 'publish';
    
    const ADD_AGREEMENT_RIGHT = 5;
    const ADD_AGREEMENT_NAME = 'add_agreement';
    
    const SUBSCRIBE_CATEGORY_RIGHT = 7;
    const SUBSCRIBE_CATEGORY_NAME = 'subscribe_category';
    
    const SUBSCRIBE_GROUP_RIGHT = 8;
    const SUBSCRIBE_GROUP_NAME = 'subscribe_group';
    
    const SUBSCRIBE_USER_RIGHT = 9;
    const SUBSCRIBE_USER_NAME = 'subscribe_user';
    
    const SUBSCRIBE_AGREEMENT_USER_RIGHT = 10;
    const SUBSCRIBE_AGREEMENT_USER_NAME = 'subscribe_agreement_user';
    
    const ADD_LOCATION_RIGHT = 11;
    const ADD_LOCATION_NAME = 'add_location';
    
    const APPROVE_LOCATION_RIGHT = 12;
    const APPROVE_LOCATION_NAME = 'approve_location';
    
    const ADD_MENTOR_RIGHT = 13;
    const ADD_MENTOR_NAME = 'add_mentor';
    
    const ADD_MOMENT_RIGHT = 14;
    const ADD_MOMENT_NAME = 'add_moment';
    
    const LOCATION_AGREEMENT = 1;
    const LOCATION_CATEGORY = 2;
    const LOCATION_ORGANISATION = 3;
    const LOCATION_PERIOD = 4;
    const LOCATION_REGION = 5;
    const LOCATION_REPORTING = 6;
    const LOCATION_APPOINTMENT = 7;
    const LOCATION_ADMINISTRATION = 8;
    
    const TREE_TYPE_INTERNSHIP_ORGANIZER = 0;
    
    const TYPE_COMPONENT = 1;
    const TYPE_PUBLICATION = 2;
    const TYPE_PERIOD = 3;
    const TYPE_AGREEMENT = 4;
    const TYPE_MOMENT = 5;
    const TYPE_LOCATION = 6;

    static function get_available_rights_for_components()
    {
        return array(self :: ADD_RIGHT_NAME => self ::RIGHT_ADD, self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: EDIT_RIGHT_NAME=> self :: RIGHT_EDIT, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE);
    }
	
    static function get_administration_locations(){
    	return array(self :: LOCATION_PERIOD, self :: LOCATION_AGREEMENT, self :: LOCATION_APPOINTMENT, self :: LOCATION_REPORTING, self :: LOCATION_ORGANISATION, self :: LOCATION_CATEGORY, self :: LOCATION_REGION);
    }
    
    static function get_available_rights_for_publications()
    {
        return array(self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE);
    }

    static function get_available_rights_for_periods()
    {
        return array(self :: PUBLISH_RIGHT_NAME => self :: RIGHT_PUBLISH, self :: ADD_AGREEMENT_NAME => self :: ADD_AGREEMENT_RIGHT, self :: SUBSCRIBE_AGREEMENT_USER_NAME => self :: SUBSCRIBE_AGREEMENT_USER_RIGHT, self :: SUBSCRIBE_CATEGORY_NAME => self :: SUBSCRIBE_CATEGORY_RIGHT, self :: SUBSCRIBE_GROUP_NAME => self :: SUBSCRIBE_GROUP_RIGHT, self :: SUBSCRIBE_USER_NAME => self :: SUBSCRIBE_USER_RIGHT);
    }

    static function get_available_rights_for_agreements()
    {
        return array(self :: PUBLISH_RIGHT_NAME => self :: RIGHT_PUBLISH, self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE, self :: EDIT_RIGHT_NAME => self :: RIGHT_EDIT, self :: ADD_LOCATION_NAME => self :: ADD_LOCATION_RIGHT, self :: APPROVE_LOCATION_NAME => self :: APPROVE_LOCATION_RIGHT, self :: ADD_MENTOR_NAME => self :: ADD_MENTOR_RIGHT, self :: ADD_MOMENT_NAME => self :: ADD_MOMENT_RIGHT);
    }

    static function get_available_rights_for_moments()
    {
        return array(self :: PUBLISH_RIGHT_NAME => self :: RIGHT_PUBLISH, self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: EDIT_RIGHT_NAME => self :: RIGHT_EDIT, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE);
    }

    static function get_available_rights_for_locations()
    {
        return array(self :: PUBLISH_RIGHT_NAME => self :: RIGHT_PUBLISH, self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: EDIT_RIGHT_NAME => self :: RIGHT_EDIT, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE);
    }

    static function create_location_in_internship_organizers_subtree($name, $identifier, $parent, $type, $return_location = false)
    {
        return RightsUtilities :: create_location($name, InternshipOrganizerManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_INTERNSHIP_ORGANIZER, $return_location);
    }

    static function get_internship_organizers_subtree_root()
    {
        return RightsUtilities :: get_root(InternshipOrganizerManager :: APPLICATION_NAME, self :: TREE_TYPE_INTERNSHIP_ORGANIZER, 0);
    }

    static function get_internship_organizers_subtree_root_id()
    {
        return RightsUtilities :: get_root_id(InternshipOrganizerManager :: APPLICATION_NAME, self :: TREE_TYPE_INTERNSHIP_ORGANIZER, 0);
    }

    static function get_location_id_by_identifier_from_internship_organizers_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_id_by_identifier(InternshipOrganizerManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_INTERNSHIP_ORGANIZER);
    }

    static function get_location_by_identifier_from_internship_organizers_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_by_identifier(InternshipOrganizerManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_INTERNSHIP_ORGANIZER);
    }

    static function is_allowed_in_internship_organizers_subtree($right, $identifier, $type, $user_id = null)
    {
        return RightsUtilities :: is_allowed($right, $identifier, $type, InternshipOrganizerManager :: APPLICATION_NAME, $user_id, 0, self :: TREE_TYPE_INTERNSHIP_ORGANIZER);
    }

    static function create_internship_organizers_subtree_root_location()
    {
        return RightsUtilities :: create_location('internship_organizers_tree', InternshipOrganizerManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_INTERNSHIP_ORGANIZER);
    }

    static function get_allowed_users($right, $identifier, $type)
    {
        return RightsUtilities :: get_allowed_users($right, $identifier, $type, InternshipOrganizerManager :: APPLICATION_NAME);
    }

}
?>