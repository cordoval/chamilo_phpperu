<?php
require_once Path :: get_application_path() . 'lib/internship_planner/category_manager/component/browser/browser_table.class.php';
require_once dirname(__FILE__) . '/../category_menu.class.php';

require_once Path :: get_application_path() . 'lib/internship_planner/category.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/category_rel_location.class.php';

class InternshipPlannerCategoryManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    
    const PARAM_CATEGORY_ID = 'category_id';
    const PARAM_CATEGORY_REL_LOCATION_ID = 'category_rel_location_id';
    const PARAM_LOCATION_ID = 'location_id';
    const PARAM_REMOVE_SELECTED = 'delete';
    const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe_selected';
    const PARAM_SUBSCRIBE_SELECTED = 'subscribe_selected';
    const PARAM_TRUNCATE_SELECTED = 'truncate';
    
    const ACTION_CREATE_CATEGORY = 'create';
    const ACTION_BROWSE_CATEGORIES = 'browse';
    const ACTION_EDIT_CATEGORY = 'edit';
    const ACTION_DELETE_CATEGORY = 'delete';
    const ACTION_MOVE_CATEGORY = 'move';
    const ACTION_TRUNCATE_CATEGORY = 'truncate';
    const ACTION_VIEW_CATEGORY = 'view';
    const ACTION_SUBSCRIBE_LOCATION_TO_CATEGORY = 'subscribe';
    const ACTION_SUBSCRIBE_LOCATION_BROWSER = 'subscribe_browser';
    const ACTION_UNSUBSCRIBE_LOCATION_FROM_CATEGORY = 'unsubscribe';

    function InternshipPlannerCategoryManager($internship_manager)
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
            
            case self :: ACTION_CREATE_CATEGORY :
                $component = InternshipPlannerCategoryManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_EDIT_CATEGORY :
                $component = InternshipPlannerCategoryManagerComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_DELETE_CATEGORY :
                $component = InternshipPlannerCategoryManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_MOVE_CATEGORY :
                $component = InternshipPlannerCategoryManagerComponent :: factory('Mover', $this);
                break;
            case self :: ACTION_TRUNCATE_CATEGORY :
                $component = InternshipPlannerCategoryManagerComponent :: factory('Truncater', $this);
                break;
            case self :: ACTION_VIEW_CATEGORY :
                $component = InternshipPlannerCategoryManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_BROWSE_CATEGORIES :
                $component = InternshipPlannerCategoryManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_UNSUBSCRIBE_LOCATION_FROM_CATEGORY :
                $component = InternshipPlannerCategoryManagerComponent :: factory('Unsubscriber', $this);
                break;
            case self :: ACTION_SUBSCRIBE_LOCATION_TO_CATEGORY :
                $component = InternshipPlannerCategoryManagerComponent :: factory('Subscriber', $this);
                break;
            case self :: ACTION_SUBSCRIBE_LOCATION_BROWSER :
                $component = InternshipPlannerCategoryManagerComponent :: factory('SubscribeLocationBrowser', $this);
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_CATEGORIES);
                $component = InternshipPlannerCategoryManagerComponent :: factory('Browser', $this);
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_planner/category_manager/component/';
    }

    //categories
    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipPlannerDataManager :: get_instance()->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function retrieve_category_rel_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipPlannerDataManager :: get_instance()->retrieve_category_rel_locations($condition, $offset, $count, $order_property);
    }

    function retrieve_category_rel_location($location_id, $category_id)
    {
        return InternshipPlannerDataManager :: get_instance()->retrieve_category_rel_location($location_id, $category_id);
    }

    function retrieve_category($id)
    {
        return InternshipPlannerDataManager :: get_instance()->retrieve_internship_planner_category($id);
    }

    function retrieve_root_category()
    {
        return InternshipPlannerDataManager :: get_instance()->retrieve_root_category();
    }

    function count_categories($conditions = null)
    {
        return InternshipPlannerDataManager :: get_instance()->count_categories($conditions);
    }

    function count_category_rel_locations($conditions = null)
    {
        return InternshipPlannerDataManager :: get_instance()->count_category_rel_locations($conditions);
    }

    //url
    

    function get_category_editing_url($category)
    {
        
    	return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CATEGORY, self :: PARAM_CATEGORY_ID => $category->get_id()));
    }

    function get_create_category_url($parent_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY, self :: PARAM_CATEGORY_ID => $parent_id));
    }

    function get_category_emptying_url($category)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TRUNCATE_CATEGORY, self :: PARAM_CATEGORY_ID => $category->get_id()));
    }

    function get_category_viewing_url($category)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CATEGORY, self :: PARAM_CATEGORY_ID => $category->get_id()));
    }

    function get_category_rel_location_unsubscribing_url($categoryrellocation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_LOCATION_FROM_CATEGORY, self :: PARAM_CATEGORY_REL_LOCATION_ID => $categoryrellocation->get_category_id() . '|' . $categoryrellocation->get_location_id()));
    }

    function get_category_rel_location_subscribing_url($category, $location)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_LOCATION_TO_CATEGORY, self :: PARAM_CATEGORY_ID => $category->get_id(), self :: PARAM_LOCATION_ID => $location->get_id()));
    }

    function get_category_suscribe_location_browser_url($category)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_LOCATION_BROWSER, self :: PARAM_CATEGORY_ID => $category->get_id()));
    }

    function get_category_delete_url($category)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CATEGORY, self :: PARAM_CATEGORY_ID => $category->get_id()));
    }

    function get_move_category_url($category)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_CATEGORY, self :: PARAM_CATEGORY_ID => $category->get_id()));
    }

    private function parse_input_from_table()
    {
        
        if (isset($_POST['action']))
        {
            
            $selected_ids = $_POST[CategoryRelLocationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            
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
                case self :: PARAM_UNSUBSCRIBE_SELECTED :
                    $this->set_category_action(self :: ACTION_UNSUBSCRIBE_LOCATION_FROM_CATEGORY);
                    Request :: set_get(self :: PARAM_CATEGORY_REL_LOCATION_ID, $selected_ids);
                    break;
                case self :: PARAM_SUBSCRIBE_SELECTED :
                    $this->set_category_action(self :: ACTION_SUBSCRIBE_LOCATION_TO_CATEGORY);
                    Request :: set_get(self :: PARAM_LOCATION_ID, $selected_ids);
                    break;
                case self :: PARAM_REMOVE_SELECTED :
                    $this->set_category_action(self :: ACTION_DELETE_CATEGORY);
                    Request :: set_get(self :: PARAM_CATEGORY_ID, $selected_ids);
                    break;
                case self :: PARAM_TRUNCATE_SELECTED :
                    $this->set_category_action(self :: ACTION_TRUNCATE_CATEGORY);
                    Request :: set_get(self :: PARAM_CATEGORY_ID, $selected_ids);
                    break;
            }
        }
    }

    private function set_category_action($action)
    {
        $this->set_parameter(self :: PARAM_ACTION, $action);
    }
}

?>