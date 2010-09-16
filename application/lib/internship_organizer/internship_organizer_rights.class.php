<?php
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

class InternshipOrganizerRights
{
    const RIGHT_VIEW = '1';
    const VIEW_RIGHT_NAME = 'view';
    const RIGHT_ADD = '2';
    const ADD_RIGHT_NAME = 'add';
    const RIGHT_EDIT = '3';
    const EDIT_RIGHT_NAME = 'edit';
    const RIGHT_DELETE = '4';
    const DELETE_RIGHT_NAME = 'delete';
    const RIGHT_PUBLISH = '5';
    const PUBLISH_RIGHT_NAME = 'publish';
    
    const PUBLISH_RIGHT = '22';
    //    const PUBLISH_RIGHT_NAME = 'publish';
    const VIEW_AGREEMENT_RIGHT = '15';
    const VIEW_AGREEMENT_NAME = 'view_agreement';
    const ADD_AGREEMENT_RIGHT = '6';
    const ADD_AGREEMENT_NAME = 'add_agreement';
    const DELETE_AGREEMENT_RIGHT = '7';
    const DELETE_AGREEMENT_NAME = 'delete_agreement';
    const EDIT_AGREEMENT_RIGHT = '8';
    const EDIT_AGREEMENT_NAME = 'edit_agreement';
    const SUBSCRIBE_CATEGORY_RIGHT = '9';
    const SUBSCRIBE_CATEGORY_NAME = 'subscribe_category';
    const UNSUBSCRIBE_CATEGORY_RIGHT = '10';
    const UNSUBSCRIBE_CATEGORY_NAME = 'unsubscribe_category';
    const SUBSCRIBE_GROUP_RIGHT = '11';
    const SUBSCRIBE_GROUP_NAME = 'subscribe_group';
    const UNSUBSCRIBE_GROUP_RIGHT = '12';
    const UNSUBSCRIBE_GROUP_NAME = 'unsubscribe_group';
    const SUBSCRIBE_USER_RIGHT = '13';
    const SUBSCRIBE_USER_NAME = 'subscribe_user';
    const UNSUBSCRIBE_USER_RIGHT = '14';
    const UNSUBSCRIBE_USER_NAME = 'unsubscribe_user';
    const SUBSCRIBE_AGREEMENT_USER_RIGHT = '16';
    const SUBSCRIBE_AGREEMENT_USER_NAME = 'subscribe_agreement_user';
    const UNSUBSCRIBE_AGREEMENT_USER_RIGHT = '17';
    const UNSUBSCRIBE_AGREEMENT_USER_NAME = 'unsubscribe_agreement_user';
    const ADD_LOCATION_RIGHT = '18';
    const ADD_LOCATION_NAME = 'add_location';
    const APPROVE_LOCATION_RIGHT = '19';
    const APPROVE_LOCATION_NAME = 'approve_location';
    const ADD_MENTOR_RIGHT = '20';
    const ADD_MENTOR_NAME = 'add_mentor';
    const ADD_MOMENT_RIGHT = '21';
    const ADD_MOMENT_NAME = 'add_moment';
    const VIEW_MOMENT_RIGHT = '23';
    const VIEW_MOMENT_NAME = 'view_moment';
    const DELETE_MOMENT_RIGHT = '24';
    const DELETE_MOMENT_NAME = 'delete_moment';
    const EDIT_MOMENT_RIGHT = '25';
    const EDIT_MOMENT_NAME = 'edit_moment';
    
    const LOCATION_AGREEMENT = 1;
    const LOCATION_CATEGORY = 2;
    const LOCATION_ORGANISATION = 3;
    const LOCATION_PERIOD = 4;
    const LOCATION_REGION = 5;
    const LOCATION_REPORTING = 6;
    
    const TREE_TYPE_INTERNSHIP_ORGANIZER = 0;
    
    const TYPE_INTERNSHIP_ORGANIZER_COMPONENT = 1;
    const TYPE_PUBLICATION = 2;
    const TYPE_PERIOD = 3;
    const TYPE_AGREEMENT = 4;
    const TYPE_MOMENT = 5;

    static function get_available_rights_for_publications()
    {
        return array(self :: VIEW_RIGHT_NAME => self :: RIGHT_VIEW, self :: EDIT_RIGHT_NAME => self :: RIGHT_EDIT, self :: DELETE_RIGHT_NAME => self :: RIGHT_DELETE);
    }

    static function get_available_rights_for_periods()
    {
        return array(self :: PUBLISH_RIGHT_NAME => self :: PUBLISH_RIGHT, self :: VIEW_AGREEMENT_NAME => self :: VIEW_AGREEMENT_RIGHT, self :: ADD_AGREEMENT_NAME => self :: ADD_AGREEMENT_RIGHT, self :: DELETE_AGREEMENT_NAME => self :: DELETE_AGREEMENT_RIGHT, self :: EDIT_AGREEMENT_NAME => self :: EDIT_AGREEMENT_RIGHT, self :: SUBSCRIBE_AGREEMENT_USER_NAME => self :: SUBSCRIBE_AGREEMENT_USER_RIGHT, self :: UNSUBSCRIBE_AGREEMENT_USER_NAME => self :: UNSUBSCRIBE_AGREEMENT_USER_RIGHT, self :: SUBSCRIBE_CATEGORY_NAME => self :: SUBSCRIBE_CATEGORY_RIGHT, self :: UNSUBSCRIBE_CATEGORY_NAME => self :: UNSUBSCRIBE_CATEGORY_RIGHT, self :: SUBSCRIBE_GROUP_NAME => self :: SUBSCRIBE_GROUP_RIGHT, self :: UNSUBSCRIBE_GROUP_NAME => self :: UNSUBSCRIBE_GROUP_RIGHT, self :: SUBSCRIBE_USER_NAME => self :: SUBSCRIBE_USER_RIGHT, self :: UNSUBSCRIBE_USER_NAME => self :: UNSUBSCRIBE_USER_RIGHT);
    }

    static function get_available_rights_for_agreements()
    {
        return array(self :: PUBLISH_RIGHT_NAME => self :: PUBLISH_RIGHT, self :: VIEW_AGREEMENT_NAME => self :: VIEW_AGREEMENT_RIGHT, self :: DELETE_AGREEMENT_NAME => self :: DELETE_AGREEMENT_RIGHT, self :: EDIT_AGREEMENT_NAME => self :: EDIT_AGREEMENT_RIGHT, self :: ADD_LOCATION_NAME => self :: ADD_LOCATION_RIGHT, self :: APPROVE_LOCATION_NAME => self :: APPROVE_LOCATION_RIGHT, self :: ADD_MENTOR_NAME => self :: ADD_MENTOR_RIGHT, self :: ADD_MOMENT_NAME => self :: ADD_MOMENT_RIGHT);
    }

    static function get_available_rights_for_moments()
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