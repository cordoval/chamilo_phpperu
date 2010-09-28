<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/category_manager/component/browser/browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/category_manager/component/rel_location_browser/rel_location_browser_table.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/category_manager/component/subscribe_location_browser/subscribe_location_browser_table.class.php';

require_once dirname(__FILE__) . '/../category_menu.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/category.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/category_rel_location.class.php';
/**
 * General category manager not good enough?
 *
 */
class InternshipOrganizerCategoryManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    
    const PARAM_CATEGORY_ID = 'category_id';
    const PARAM_CATEGORY_REL_LOCATION_ID = 'category_rel_location_id';
    const PARAM_LOCATION_ID = 'location_id';
    
    const PARAM_MESSAGE = 'message';
    const PARAM_WARNING_MESSAGE = 'warning_message';
    const PARAM_ERROR_MESSAGE = 'error_message';
    
    const ACTION_CREATE_CATEGORY = 'creator';
    const ACTION_BROWSE_CATEGORIES = 'browser';
    const ACTION_EDIT_CATEGORY = 'editor';
    const ACTION_DELETE_CATEGORY = 'deleter';
    const ACTION_MOVE_CATEGORY = 'mover';
    const ACTION_TRUNCATE_CATEGORY = 'truncater';
    const ACTION_SUBSCRIBE_LOCATION_TO_CATEGORY = 'subscriber';
    const ACTION_SUBSCRIBE_LOCATION_BROWSER = 'subscribe_location_browser';
    const ACTION_UNSUBSCRIBE_LOCATION_FROM_CATEGORY = 'unsubscriber';
    const ACTION_IMPORT_CATEGORY = 'importer';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_CATEGORIES;

    function InternshipOrganizerCategoryManager($internship_manager)
    {
        parent :: __construct($internship_manager);
        $action = Request :: get(self :: PARAM_ACTION);
        
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/internship_organizer/category_manager/component/';
    }

    //categories
    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function retrieve_category_rel_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_category_rel_locations($condition, $offset, $count, $order_property);
    }

    function count_category_rel_locations($condition = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_category_rel_locations($condition);
    }

    function retrieve_category_rel_location($location_id, $category_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_category_rel_location($location_id, $category_id);
        //        return InternshipOrganizerDataManager :: get_instance()->retrieve_category_rel_location($location_id, $category_id);
    }

    function retrieve_category($id)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_internship_organizer_category($id);
    }

    function retrieve_root_category()
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_root_category();
    }

    function count_categories($conditions = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_categories($conditions);
    }

    //url
    

    function get_browse_categories_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES));
    }

    function get_category_editing_url($category)
    {
        
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CATEGORY, self :: PARAM_CATEGORY_ID => $category->get_id()));
    }

    function get_category_create_url()
    {
        
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY));
    }

    function get_create_category_url($parent_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY, self :: PARAM_CATEGORY_ID => $parent_id));
    }

    function get_category_emptying_url($category)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TRUNCATE_CATEGORY, self :: PARAM_CATEGORY_ID => $category->get_id()));
    }

    function get_category_rel_location_unsubscribing_url($categoryrellocation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_LOCATION_FROM_CATEGORY, self :: PARAM_CATEGORY_REL_LOCATION_ID => $categoryrellocation->get_category_id() . '|' . $categoryrellocation->get_location_id()));
    }

    function get_category_rel_location_subscribing_url($category, $location)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_LOCATION_TO_CATEGORY, self :: PARAM_CATEGORY_REL_LOCATION_ID => $category->get_id() . '|' . $location->get_id()));
    }

    function get_category_subscribe_location_browser_url($category)
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

    function get_category_importer_url($category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id));
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