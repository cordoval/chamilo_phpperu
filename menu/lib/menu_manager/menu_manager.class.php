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

    const ACTION_SORT_MENU = 'sorter';

    const ACTION_COMPONENT_BROWSE_CATEGORY = 'browse';
    const ACTION_COMPONENT_ADD_CATEGORY = 'add';
    const ACTION_COMPONENT_EDIT_CATEGORY = 'edit';
    const ACTION_COMPONENT_DELETE_CATEGORY = 'delete';
    const ACTION_COMPONENT_MOVE_CATEGORY = 'move';
    const ACTION_COMPONENT_CAT_EDIT = 'edit_category';
    const ACTION_COMPONENT_CAT_ADD = 'add_category';

    const DEFAULT_ACTION = self :: ACTION_SORT_MENU;

    private $parameters;
    private $user;
    private $breadcrumbs;

    function MenuManager($user)
    {
        parent :: __construct($user);
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

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('Manage'), Translation :: get('ManageDescription'), Theme :: get_image_path() . 'browse_sort.png', Redirect :: get_link(self :: APPLICATION_NAME, array(Application :: PARAM_ACTION => self :: ACTION_SORT_MENU), array(), false, Redirect :: TYPE_CORE));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
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

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}
?>