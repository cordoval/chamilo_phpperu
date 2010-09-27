<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/browser/browser_table.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/location_browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation.class.php';

class InternshipOrganizerOrganisationManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    const PARAM_ORGANISATION_ID = 'organisation_id';
    const PARAM_ORGANISATION_REL_USER_ID = 'organisation_rel_user_id';
    
    const PARAM_LOCATION_ID = 'location_id';
    const PARAM_REGION_ID = 'region_id';
    
    const PARAM_MENTOR_ID = 'mentor_id';
    const PARAM_MENTOR_REL_LOCATION_ID = 'mentor_rel_location_id';
    const PARAM_MENTOR_REL_USER_ID = 'mentor_rel_user_id';
    
    const PARAM_PUBLICATION_ID = 'publication_id';
    
    const PARAM_MESSAGE = 'message';
    const PARAM_WARNING_MESSAGE = 'warning_message';
    const PARAM_ERROR_MESSAGE = 'error_message';
    
    const ACTION_CREATE_ORGANISATION = 'creator';
    const ACTION_BROWSE_ORGANISATION = 'browser';
    const ACTION_EDIT_ORGANISATION = 'editor';
    const ACTION_DELETE_ORGANISATION = 'deleter';
    const ACTION_VIEW_ORGANISATION = 'viewer';
    
    const ACTION_CREATE_LOCATION = 'location_creator';
    const ACTION_EDIT_LOCATION = 'location_editor';
    const ACTION_DELETE_LOCATION = 'location_deleter';
    const ACTION_VIEW_LOCATION = 'location_viewer';
    const ACTION_PUBLISH_LOCATION = 'publisher';
    
    const ACTION_CREATE_MENTOR = 'mentor_creator';
    const ACTION_EDIT_MENTOR = 'mentor_editor';
    const ACTION_DELETE_MENTOR = 'mentor_deleter';
    const ACTION_SUBSCRIBE_LOCATION = 'subscribe_location';
    const ACTION_UNSUBSCRIBE_LOCATION = 'unsubscribe_location';
    const ACTION_SUBSCRIBE_MENTOR_USERS = 'subscribe_mentor_user';
    const ACTION_UNSUBSCRIBE_MENTOR_USER = 'unsubscribe_mentor_user';
    const ACTION_VIEW_MENTOR = 'mentor_viewer';
    
    const ACTION_SUBSCRIBE_USER = 'subscribe_user';
    const ACTION_UNSUBSCRIBE_USER = 'unsubscribe_user';
    
    const ACTION_VIEW_PUBLICATION = 'publication_viewer';
    const ACTION_DELETE_PUBLICATION = 'publication_deleter';
    const ACTION_EDIT_PUBLICATION_RIGHTS = 'publication_rights_editor';
    
    const ACTION_IMPORT_ORGANISATION = 'importer';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_ORGANISATION;

    function InternshipOrganizerOrganisationManager($internship_manager)
    {
        parent :: __construct($internship_manager);
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/';
    }

    //organisations
    

    function count_organisations($condition)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_organisations($condition);
    }

    function retrieve_organisations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_organisations($condition, $offset, $count, $order_property);
    }

    function retrieve_organisation($id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_organisation($id);
    }

    //locations
    function count_locations($condition)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_locations($condition);
    }

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_locations($condition, $offset, $count, $order_property);
    }

    function retrieve_location($id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_location($id);
    }

    function retrieve_internship_organizer_region($region_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_internship_organizer_region($region_id);
    }

    //mentors
    

    function count_mentors($condition)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_mentors($condition);
    }

    function retrieve_mentors($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_mentors($condition, $offset, $count, $order_property);
    }

    function retrieve_mentor($id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_mentor($id);
    }

    //url creation
    function get_create_organisation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ORGANISATION));
    }

    function get_update_organisation_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
    }

    function get_delete_organisation_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
    }

    function get_browse_organisations_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION));
    }

    function get_view_organisation_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
    }

    function get_create_location_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LOCATION, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
    }

    function get_view_location_url($location)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_LOCATION, self :: PARAM_LOCATION_ID => $location->get_id()));
    }

    function get_update_location_url($location)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LOCATION, self :: PARAM_LOCATION_ID => $location->get_id()));
    }

    function get_delete_location_url($location)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LOCATION, self :: PARAM_LOCATION_ID => $location->get_id()));
    }

    function get_create_mentor_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_MENTOR, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
    }

    function get_update_mentor_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    function get_delete_mentor_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    function get_subscribe_locations_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_LOCATION, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    function get_unsubscribe_location_url($mentor_rel_location)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_LOCATION, self :: PARAM_MENTOR_REL_LOCATION_ID => $mentor_rel_location->get_mentor_id() . '|' . $mentor_rel_location->get_location_id()));
    }

    function get_view_mentor_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    function get_subscribe_mentor_users_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_MENTOR_USERS, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    function get_unsubscribe_mentor_user_url($mentor_rel_user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_MENTOR_USER, self :: PARAM_MENTOR_REL_USER_ID => $mentor_rel_user->get_mentor_id() . '|' . $mentor_rel_user->get_user_id()));
    }

    function get_subscribe_users_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
    }

    function get_unsubscribe_user_url($organisation_rel_user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_USER, self :: PARAM_ORGANISATION_REL_USER_ID => $organisation_rel_user->get_organisation_id() . '|' . $organisation_rel_user->get_user_id()));
    }

    function get_organisation_publish_url($organisation_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_LOCATION, self :: PARAM_ORGANISATION_ID => $organisation_id));
    }

    function get_location_publish_url($location_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_LOCATION, self :: PARAM_LOCATION_ID => $location_id));
    }

    function get_view_publication_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PUBLICATION_ID => $publication->get_id()));
    }

    function get_delete_publication_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_PUBLICATION_ID => $publication->get_id()));
    }

    function get_publication_rights_editor_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PUBLICATION_RIGHTS, self :: PARAM_PUBLICATION_ID => $publication->get_id()));
    }

    function get_organisation_importer_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_ORGANISATION));
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