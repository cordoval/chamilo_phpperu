<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/browser/browser_table.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/location_browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation.class.php';

class InternshipOrganizerOrganisationManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    const PARAM_ORGANISATION_ID = 'organisation_id';
    const PARAM_DELETE_SELECTED_ORGANISATIONS = 'delete_organisations';
    
    const PARAM_LOCATION_ID = 'location_id';
    const PARAM_REGION_ID = 'region_id';
    const PARAM_DELETE_SELECTED_LOCATIONS = 'delete_locations';
    
    const PARAM_MENTOR_ID = 'mentor_id';
    const PARAM_DELETE_SELECTED_MENTORS = 'delete_mentors';
    
    const ACTION_CREATE_ORGANISATION = 'creator';
    const ACTION_BROWSE_ORGANISATION = 'browser';
    const ACTION_UPDATE_ORGANISATION = 'updater';
    const ACTION_DELETE_ORGANISATION = 'deleter';
    const ACTION_VIEW_ORGANISATION = 'viewer';
    
    const ACTION_CREATE_LOCATION = 'location_creator';
    //    const ACTION_BROWSE_LOCATIONS = 'location_browser';
    const ACTION_EDIT_LOCATION = 'location_updater';
    const ACTION_DELETE_LOCATION = 'location_deleter';
    const ACTION_VIEW_LOCATION = 'location_viewer';
    
    const ACTION_CREATE_MENTOR = 'mentor_creator';
    //    const ACTION_BROWSE_MENTOR = 'mentor_browser';
    const ACTION_UPDATE_MENTOR = 'mentor_updater';
    const ACTION_DELETE_MENTOR = 'mentor_deleter';
    const ACTION_VIEW_MENTOR = 'mentor_viewer';
    
    const ACTION_SUBSCRIBE_USERS = 'subscribe_users';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_ORGANISATION;

    function InternshipOrganizerOrganisationManager($internship_manager)
    {
        parent :: __construct($internship_manager);
        $action = Request :: get(self :: PARAM_ACTION);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
        $this->parse_input_from_table();
    
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
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
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
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_LOCATION, self :: PARAM_LOCATION_ID => $location->get_id(), self :: PARAM_REGION_ID => $location->get_region_id(), self :: PARAM_ORGANISATION_ID => $location->get_organisation_id()));
    }

    function get_update_location_url($location)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LOCATION, self :: PARAM_LOCATION_ID => $location->get_id()));
    }

    function get_delete_location_url($location)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LOCATION, self :: PARAM_LOCATION_ID => $location->get_id()));
    }

    //    function get_browse_locations_url()
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_LOCATIONS));
    //    }
    

    function get_create_mentor_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_MENTOR, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
    }

    function get_update_mentor_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    function get_delete_mentor_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    //    function get_browse_mentors_url()
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_MENTOR));
    //    }
    

    function get_view_mentor_url($mentor)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id()));
    }

    function get_organisation_subscribe_users_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USERS, self :: PARAM_ORGANISATION_ID => $organisation->get_id()));
    }

    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            if (isset($_POST[InternshipOrganizerOrganisationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[InternshipOrganizerOrganisationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            }
            
            if (isset($_POST[InternshipOrganizerLocationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[InternshipOrganizerLocationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            }
            
            if (isset($_POST[InternshipOrganizerMentorBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
            {
                $selected_ids = $_POST[InternshipOrganizerMentorBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
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
                case self :: PARAM_DELETE_SELECTED_LOCATIONS :
                    $this->set_organisation_action(self :: ACTION_DELETE_LOCATION);
                    $_GET[self :: PARAM_LOCATION_ID] = $selected_ids;
                    break;
                case self :: PARAM_DELETE_SELECTED_ORGANISATIONS :
                    $this->set_organisation_action(self :: ACTION_DELETE_ORGANISATION);
                    $_GET[self :: PARAM_ORGANISATION_ID] = $selected_ids;
                    break;
                case self :: PARAM_DELETE_SELECTED_MENTORS :
                    $this->set_organisation_action(self :: ACTION_DELETE_MENTOR);
                    $_GET[self :: PARAM_MENTOR_ID] = $selected_ids;
                    break;
            }
        }
    }

    private function set_organisation_action($action)
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