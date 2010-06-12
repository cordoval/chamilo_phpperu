<?php
/**
 * $Id: menu_manager.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.menu_manager.component
 */

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class MenuManager extends CoreApplication
{
    const APPLICATION_NAME = 'menu';
    
    const PARAM_ERROR_MESSAGE = 'error_message';
    const PARAM_COMPONENT_ACTION = 'action';
    const PARAM_DIRECTION = 'direction';
    const PARAM_CATEGORY = 'category';
    
    const ACTION_RENDER_BAR = 'render_bar';
    const ACTION_RENDER_MINI_BAR = 'render_mini_bar';
    const ACTION_RENDER_TREE = 'render_tree';
    const ACTION_RENDER_SITEMAP = 'render_sitemap';
    const ACTION_SORT_MENU = 'sort';
    
    const ACTION_COMPONENT_BROWSE_CATEGORY = 'browse';
    const ACTION_COMPONENT_ADD_CATEGORY = 'add';
    const ACTION_COMPONENT_EDIT_CATEGORY = 'edit';
    const ACTION_COMPONENT_DELETE_CATEGORY = 'delete';
    const ACTION_COMPONENT_MOVE_CATEGORY = 'move';
    const ACTION_COMPONENT_CAT_EDIT = 'edit_category';
    const ACTION_COMPONENT_CAT_ADD = 'add_category';
    
    private $parameters;
    private $user;
    private $breadcrumbs;

    function MenuManager($user)
    {
        parent :: __construct($user);
    }

    /**
     * Run this user manager
     */
    function run()
    {
        /*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
        //$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_SORT_MENU :
                $component = $this->create_component('Sorter');
                break;
            default :
                $this->set_action(self :: ACTION_SORT_MENU);
                $component = $this->create_component('Sorter');
        }
        $component->run();
    }

    function render_menu($type)
    {
        switch ($type)
        {
            case self :: ACTION_RENDER_BAR :
                $component = $this->create_component('Bar');
                break;
            case self :: ACTION_RENDER_MINI_BAR :
                $component = $this->create_component('MiniBar');
                break;
            case self :: ACTION_RENDER_TREE :
                $component = $this->create_component('Tree');
                break;
            case self :: ACTION_RENDER_SITEMAP :
                $component = $this->create_component('Sitemap');
                break;
            default :
                $this->set_action(self :: ACTION_RENDER_BAR);
                $component = $this->create_component('Bar');
        }
        return $component->run();
    }

    function count_navigation_items($condition = null)
    {
        return MenuDataManager :: get_instance()->count_navigation_items($condition);
    }

    function retrieve_navigation_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return MenuDataManager :: get_instance()->retrieve_navigation_items($condition, $offset, $count, $order_property);
    }

    function retrieve_navigation_item($id)
    {
        return MenuDataManager :: get_instance()->retrieve_navigation_item($id);
    }

    function retrieve_navigation_item_at_sort($parent, $sort, $direction)
    {
        return MenuDataManager :: get_instance()->retrieve_navigation_item_at_sort($parent, $sort, $direction);
    }

    public function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('Manage'), Translation :: get('ManageDescription'), Theme :: get_image_path() . 'browse_sort.png', $this->get_link(array(Application :: PARAM_ACTION => MenuManager :: ACTION_SORT_MENU)));
        
        $info = parent :: get_application_platform_admin_links();
        $info['links'] = $links;
        
        return $info;
    }

    function get_navigation_item_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_ADD_CATEGORY));
    }

    function get_category_navigation_item_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_CAT_ADD));
    }

    function get_navigation_item_editing_url($navigation_item)
    {
        if ($navigation_item->get_is_category())
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_CAT_EDIT, self :: PARAM_CATEGORY => $navigation_item->get_id()));
        }
        
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_EDIT_CATEGORY, self :: PARAM_CATEGORY => $navigation_item->get_id()));
    }

    function get_navigation_item_deleting_url($navigation_item)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_DELETE_CATEGORY, self :: PARAM_CATEGORY => $navigation_item->get_id()));
    }

    function get_navigation_item_moving_url($navigation_item, $direction)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SORT_MENU, self :: PARAM_COMPONENT_ACTION => self :: ACTION_COMPONENT_MOVE_CATEGORY, self :: PARAM_CATEGORY => $navigation_item->get_id(), self :: PARAM_DIRECTION => $direction));
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }
}
?>