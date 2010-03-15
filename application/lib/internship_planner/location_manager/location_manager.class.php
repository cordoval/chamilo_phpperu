<?php
require_once Path :: get_application_path() . 'lib/internship_planner/location_manager/component/browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/location.class.php';

class InternshipLocationManager extends SubManager
{
    
    const PARAM_LOCATION_ACTION = 'action';
    const PARAM_LOCATION_ID = 'location_id';
  
       
    const ACTION_CREATE_LOCATION = 'create_location';
    const ACTION_BROWSE_LOCATIONS = 'browse_locations';
    const ACTION_EDIT_LOCATION = 'edit_location';
    const ACTION_DELETE_LOCATION = 'delete_location';
   
    
    
    function InternshipLocationManager($internship_manager)
    {
        parent :: __construct($internship_manager);
        $location_action = Request :: get(self :: PARAM_LOCATION_ACTION);
        if ($location_action)
        {
            $this->set_parameter(self :: PARAM_LOCATION_ACTION, $location_action);
        }
        $this->parse_input_from_table();
    
    }

    function run()
    {
        $location_action = $this->get_parameter(self :: PARAM_LOCATION_ACTION);
        
        switch ($location_action)
        {
            
            case self :: ACTION_EDIT_LOCATION :
                $component = InternshipLocationManagerComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_DELETE_LOCATION :
                $component = InternshipLocationManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_CREATE_LOCATION :
                $component = InternshipLocationManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_BROWSE_LOCATIONS :
                $component = InternshipLocationManagerComponent :: factory('Browser', $this);
                break;
            default :
                $component = InternshipLocationManagerComponent :: factory('Browser', $this);
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_planner/location_manager/component/';
    }

    //location
    
	function count_locations($condition)
	{
		return InternshipPlannerDataManager :: get_instance()->count_locations($condition);
	}

	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_locations($condition, $offset, $count, $order_property);
	}

 	function retrieve_location($id)
	{
		return InternshipPlannerDataManager :: get_instance()->retrieve_location($id);
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
        $this->set_parameter(self :: PARAM_LOCATION_ACTION, $action);
    }
}

?>