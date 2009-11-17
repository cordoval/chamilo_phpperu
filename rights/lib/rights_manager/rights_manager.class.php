<?php
/**
 * $Id: rights_manager.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class RightsManager extends CoreApplication
{
    const APPLICATION_NAME = 'rights';
    
    const PARAM_REMOVE_SELECTED = 'delete';
    const PARAM_FIRSTLETTER = 'firstletter';
    const PARAM_COMPONENT_ACTION = 'action';
    
    const ACTION_MANAGE_RIGHTS_TEMPLATES = 'template';
    const ACTION_MANAGE_USER_RIGHTS = 'user';
    const ACTION_MANAGE_GROUP_RIGHTS = 'group';
    const ACTION_MANAGE_LOCATIONS = 'location';
    const ACTION_REQUEST_RIGHT = 'request_rights';
    
    private $quota_url;
    private $publication_url;
    private $create_url;
    private $recycle_bin_url;

    function RightsManager($user = null)
    {
        parent :: __construct($user);
        //$this->create_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_USER));
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
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
            case self :: ACTION_MANAGE_RIGHTS_TEMPLATES :
                $component = RightsManagerComponent :: factory('Templater', $this);
                break;
            case self :: ACTION_MANAGE_USER_RIGHTS :
                $component = RightsManagerComponent :: factory('User', $this);
                break;
            case self :: ACTION_MANAGE_GROUP_RIGHTS :
                $component = RightsManagerComponent :: factory('Group', $this);
                break;
            case self :: ACTION_MANAGE_LOCATIONS :
                $component = RightsManagerComponent :: factory('Locater', $this);
                break;
            case self :: ACTION_REQUEST_RIGHT :
                $component = RightsManagerComponent :: factory('RightRequester', $this);
                break;
            default :
                $this->set_action(self :: ACTION_MANAGE_RIGHTS_TEMPLATES);
                $component = RightsManagerComponent :: factory('Templater', $this);
        }
        $component->run();
    }

    function retrieve_rights_templates($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return RightsDataManager :: get_instance()->retrieve_rights_templates($condition, $offset, $count, $order_property);
    }

    function count_rights_templates($condition = null)
    {
        return RightsDataManager :: get_instance()->count_rights_templates($condition);
    }

    function count_locations($condition = null)
    {
        return RightsDataManager :: get_instance()->count_locations($condition);
    }

    function delete_rights_template($rights_template)
    {
        return RightsDataManager :: get_instance()->delete_rights_template($rights_template);
    }

    function retrieve_rights($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return RightsDataManager :: get_instance()->retrieve_rights($condition, $offset, $count, $order_property);
    }

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return RightsDataManager :: get_instance()->retrieve_locations($condition, $offset, $count, $order_property);
    }

    function retrieve_rights_template($id)
    {
        return RightsDataManager :: get_instance()->retrieve_rights_template($id);
    }

    function retrieve_location($id)
    {
        return RightsDataManager :: get_instance()->retrieve_location($id);
    }

    function retrieve_right($id)
    {
        return RightsDataManager :: get_instance()->retrieve_right($id);
    }

    public function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = array('name' => Translation :: get('Locations'), 'description' => Translation :: get('LocationsDescription'), 'action' => 'location', 'url' => $this->get_link(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_LOCATIONS)));
        $links[] = array('name' => Translation :: get('RightsTemplates'), 'description' => Translation :: get('RightsTemplatesDescription'), 'action' => 'template', 'url' => $this->get_link(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)));
        $links[] = array('name' => Translation :: get('RightsTemplatePermissions'), 'description' => Translation :: get('RightsTemplatePermissionsDescription'), 'action' => 'permission_template', 'url' => $this->get_link(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_LOCATION_RIGHTS_TEMPLATES)));
        $links[] = array('name' => Translation :: get('UserPermissions'), 'description' => Translation :: get('UserPermissionsDescription'), 'action' => 'permission_user', 'url' => $this->get_link(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_LOCATION_USER_RIGHTS)));
        $links[] = array('name' => Translation :: get('GroupPermissions'), 'description' => Translation :: get('GroupPermissionsDescription'), 'action' => 'permission_group', 'url' => $this->get_link(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_BROWSE_LOCATION_GROUP_RIGHTS)));
        
        $info = parent :: get_application_platform_admin_links();
        $info['links'] = $links;
        
        return $info;
    }

    function retrieve_rights_template_right_location($right_id, $rights_template_id, $location_id)
    {
        return RightsDataManager :: get_instance()->retrieve_rights_template_right_location($right_id, $rights_template_id, $location_id);
    }

    function retrieve_user_right_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return RightsDataManager :: get_instance()->retrieve_user_right_locations($condition, $offset, $count, $order_property);
    }

    function retrieve_group_right_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return RightsDataManager :: get_instance()->retrieve_group_right_locations($condition, $offset, $count, $order_property);
    }

    function retrieve_group_right_location($right_id, $group_id, $location_id)
    {
        return RightsDataManager :: get_instance()->retrieve_group_right_location($right_id, $group_id, $location_id);
    }

    function retrieve_user_right_location($right_id, $user_id, $location_id)
    {
        return RightsDataManager :: get_instance()->retrieve_user_right_location($right_id, $user_id, $location_id);
    }
}
?>