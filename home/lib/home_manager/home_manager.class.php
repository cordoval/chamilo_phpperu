<?php
/**
 * $Id: home_manager.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class HomeManager extends CoreApplication
{
    const APPLICATION_NAME = 'home';

    const PARAM_HOME_ID = 'id';
    const PARAM_HOME_TYPE = 'type';
    const PARAM_DIRECTION = 'direction';
    const PARAM_TAB_ID = 'tab';

    const ACTION_VIEW_HOME = 'home';
    const ACTION_BUILD_HOME = 'builder';
    const ACTION_MANAGE_HOME = 'manager';
    const ACTION_EDIT_HOME = 'editor';
    const ACTION_DELETE_HOME = 'deleter';
    const ACTION_MOVE_HOME = 'mover';
    const ACTION_CREATE_HOME = 'creator';
    const ACTION_CONFIGURE_HOME = 'configurer';

    const DEFAULT_ACTION = self :: ACTION_MANAGE_HOME;

    const TYPE_BLOCK = 'block';
    const TYPE_COLUMN = 'column';
    const TYPE_ROW = 'row';
    const TYPE_TAB = 'tab';

    private $parameters;
    private $user;
    private $breadcrumbs;

    function HomeManager($user = null)
    {
        parent :: __construct($user);
    }

    function render_menu($type)
    {
        switch ($type)
        {
            case self :: ACTION_VIEW_HOME :
                $component = $this->create_component('Home');
                break;
            default :
                $this->set_action(self :: ACTION_VIEW_HOME);
                $component = $this->create_component('Home');
        }
        $component->run();
    }

    function count_home_rows($condition = null)
    {
        return HomeDataManager :: get_instance()->count_home_rows($condition);
    }

    function count_home_columns($condition = null)
    {
        return HomeDataManager :: get_instance()->count_home_columns($condition);
    }

    function count_home_blocks($condition = null)
    {
        return HomeDataManager :: get_instance()->count_home_blocks($condition);
    }

    function retrieve_home_rows($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return HomeDataManager :: get_instance()->retrieve_home_rows($condition, $offset, $count, $order_property);
    }

    function retrieve_home_tabs($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return HomeDataManager :: get_instance()->retrieve_home_tabs($condition, $offset, $count, $order_property);
    }

    function retrieve_home_columns($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return HomeDataManager :: get_instance()->retrieve_home_columns($condition, $offset, $count, $order_property);
    }

    function retrieve_home_blocks($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return HomeDataManager :: get_instance()->retrieve_home_blocks($condition, $offset, $count, $order_property);
    }

    function retrieve_home_block($id)
    {
        return HomeDataManager :: get_instance()->retrieve_home_block($id);
    }

    function retrieve_home_column($id)
    {
        return HomeDataManager :: get_instance()->retrieve_home_column($id);
    }

    function retrieve_home_row($id)
    {
        return HomeDataManager :: get_instance()->retrieve_home_row($id);
    }

    function retrieve_home_tab($id)
    {
        return HomeDataManager :: get_instance()->retrieve_home_tab($id);
    }

    function truncate_home($user_id)
    {
        return HomeDataManager :: get_instance()->truncate_home($user_id);
    }

    function retrieve_home_block_config($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return HomeDataManager :: get_instance()->retrieve_home_block_config($condition, $offset, $count, $order_property);
    }

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('Manage'), Translation :: get('ManageDescription'), Theme :: get_image_path() . 'browse_manage.png', Redirect :: get_link(self :: APPLICATION_NAME, array(Application :: PARAM_ACTION => self :: ACTION_MANAGE_HOME), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('Build'), Translation :: get('BuildDescription'), Theme :: get_image_path() . 'browse_build.png', Redirect :: get_link(self :: APPLICATION_NAME, array(Application :: PARAM_ACTION => self :: ACTION_BUILD_HOME), array(), false, Redirect :: TYPE_CORE));
        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;

        return $info;
    }

    function get_home_tab_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_TAB));
    }

    function get_home_row_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_ROW));
    }

    function get_home_block_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_BLOCK));
    }

    function get_home_column_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_COLUMN));
    }

    function get_home_tab_editing_url($home_tab)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_TAB, self :: PARAM_HOME_ID => $home_tab->get_id()));
    }

    function get_home_row_editing_url($home_row)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_ROW, self :: PARAM_HOME_ID => $home_row->get_id()));
    }

    function get_home_block_editing_url($home_block)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_BLOCK, self :: PARAM_HOME_ID => $home_block->get_id()));
    }

    function get_home_block_configuring_url($home_block)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONFIGURE_HOME, self :: PARAM_HOME_ID => $home_block->get_id()));
    }

    function get_home_column_editing_url($home_column)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_COLUMN, self :: PARAM_HOME_ID => $home_column->get_id()));
    }

    function get_home_tab_viewing_url($home_tab)
    {
        return $this->get_url(array(self :: PARAM_TAB_ID => $home_tab->get_id()));
    }

    function get_home_tab_deleting_url($home_tab)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_TAB, self :: PARAM_HOME_ID => $home_tab->get_id()));
    }

    function get_home_row_deleting_url($home_row)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_ROW, self :: PARAM_HOME_ID => $home_row->get_id()));
    }

    function get_home_block_deleting_url($home_block)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_BLOCK, self :: PARAM_HOME_ID => $home_block->get_id()));
    }

    function get_home_column_deleting_url($home_column)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_COLUMN, self :: PARAM_HOME_ID => $home_column->get_id()));
    }

    function get_home_column_moving_url($home_column, $direction)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_COLUMN, self :: PARAM_HOME_ID => $home_column->get_id(), self :: PARAM_DIRECTION => $direction));
    }

    function get_home_block_moving_url($home_block, $direction)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_BLOCK, self :: PARAM_HOME_ID => $home_block->get_id(), self :: PARAM_DIRECTION => $direction));
    }

    function get_home_tab_moving_url($home_tab, $direction)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_TAB, self :: PARAM_HOME_ID => $home_tab->get_id(), self :: PARAM_DIRECTION => $direction));
    }

    function get_home_row_moving_url($home_row, $direction)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_HOME, self :: PARAM_HOME_TYPE => self :: TYPE_ROW, self :: PARAM_HOME_ID => $home_row->get_id(), self :: PARAM_DIRECTION => $direction));
    }

    function retrieve_home_block_at_sort($parent, $sort, $direction)
    {
        $hdm = HomeDataManager :: get_instance();
        return $hdm->retrieve_home_block_at_sort($parent, $sort, $direction);
    }

    function retrieve_home_column_at_sort($parent, $sort, $direction)
    {
        $hdm = HomeDataManager :: get_instance();
        return $hdm->retrieve_home_column_at_sort($parent, $sort, $direction);
    }

    function retrieve_home_row_at_sort($parent, $sort, $direction)
    {
        $hdm = HomeDataManager :: get_instance();
        return $hdm->retrieve_home_row_at_sort($parent, $sort, $direction);
    }

    function retrieve_home_tab_at_sort($user, $sort, $direction)
    {
        $hdm = HomeDataManager :: get_instance();
        return $hdm->retrieve_home_tab_at_sort($user, $sort, $direction);
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