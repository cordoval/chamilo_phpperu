<?php
//require_once Path :: get_application_path() . 'lib/internship_planner/location_manager/component/browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/organisation.class.php';

class InternshipOrganisationManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    const PARAM_ORGANISATION_ID = 'organisation_id';
  	const PARAM_DELETE_SELECTED_ORGANISATIONS = 'delete_organisations';
    
       
    const ACTION_CREATE_ORGANISATION = 'create';
    const ACTION_BROWSE_ORGANISATION = 'browse';
    const ACTION_UPDATE_ORGANISATION = 'update';
    const ACTION_DELETE_ORGANISATION = 'delete';
   
    
    
    function InternshipOrganisationManager($internship_manager)
    {
        parent :: __construct($internship_manager);
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
            
            case self :: ACTION_UPDATE_ORGANISATION :
                $component = InternshipOrganisationManagerComponent :: factory('Updater', $this);
                break;
            case self :: ACTION_DELETE_ORGANISATION :
                $component = InternshipOrganisationManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_CREATE_ORGANISATION :
                $component = InternshipOrganisationManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_BROWSE_ORGANISATION :
                $component = InternshipOrganisationManagerComponent :: factory('Browser', $this);
                break;
            default :
                $component = InternshipOrganisationManagerComponent :: factory('Browser', $this);
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_planner/organisation_manager/component/';
    }

    //location
    
	function count_organisations($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_organisations($condition);
	}

	function retrieve_organisations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_organisations($condition, $offset, $count, $order_property);
	}

 	function retrieve_organisation($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_organisation($id);
	}

        //url creation
    function get_create_organisation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ORGANISATION));
    }

    function get_update_organisation_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_ORGANISATION, self :: PARAM_LOCATION => $organisation->get_id()));
    }

    function get_delete_organisation_url($organisation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_ORGANISATION, self :: PARAM_LOCATION => $organisation->get_id()));
    }

    function get_browse_organisations_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION));
    }
	
	
    private function parse_input_from_table()
    {
        
        if (isset($_POST['action']))
        {
            
//            if (isset($_POST[StsGroupRelUserBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
//            {
//                $selected_ids = $_POST[StsGroupRelUserBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
//            }
//            
//            if (isset($_POST[StsGroupBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
//            {
//                $selected_ids = $_POST[StsGroupBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
//            }
//            
//            if (isset($_POST[StsTrackerRelUserBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX]))
//            {
//                $selected_ids = $_POST[StsTrackerRelUserBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
//            }
//            
//            if (empty($selected_ids))
//            {
//                $selected_ids = array();
//            }
//            elseif (! is_array($selected_ids))
//            {
//                $selected_ids = array($selected_ids);
//            }
//            switch ($_POST['action'])
//            {
//                case self :: PARAM_UNSUBSCRIBE_SELECTED :
//                    $this->set_group_action(self :: ACTION_UNSUBSCRIBE_USER_FROM_GROUP);
//                    $_GET[self :: PARAM_GROUP_REL_STUDENT_ID] = $selected_ids;
//                    break;
//                case self :: PARAM_SUBSCRIBE_SELECTED :
//                    $this->set_group_action(self :: ACTION_SUBSCRIBE_USER_TO_GROUP);
//                    $_GET[StsManager :: PARAM_USER_ID] = $selected_ids;
//                    break;
//                case self :: PARAM_REMOVE_SELECTED :
//                    $this->set_group_action(self :: ACTION_DELETE_GROUP);
//                    $_GET[self :: PARAM_GROUP_ID] = $selected_ids;
//                    break;
//                case self :: PARAM_TRUNCATE_SELECTED :
//                    $this->set_group_action(self :: ACTION_TRUNCATE_GROUP);
//                    $_GET[self :: PARAM_GROUP_ID] = $selected_ids;
//                    break;
//            
//            }
        }
    }

    private function set_location_action($action)
    {
        $this->set_parameter(self :: PARAM_ACTION, $action);
    }
}

?>