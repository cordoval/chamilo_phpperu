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

    const ACTION_MANAGE_TYPE_TEMPLATES = 'type_templater';
    const ACTION_MANAGE_RIGHTS_TEMPLATES = 'templater';
    const ACTION_MANAGE_USER_RIGHTS = 'user';
    const ACTION_MANAGE_GROUP_RIGHTS = 'group';
    const ACTION_MANAGE_LOCATIONS = 'locater';
    const ACTION_REQUEST_RIGHT = 'right_requester';

    const DEFAULT_ACTION = self :: ACTION_MANAGE_RIGHTS_TEMPLATES;

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

    function retrieve_rights_templates($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return RightsDataManager :: get_instance()->retrieve_rights_templates($condition, $offset, $count, $order_property);
    }

    function retrieve_type_templates($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return RightsDataManager :: get_instance()->retrieve_type_templates($condition, $offset, $count, $order_property);
    }

    function count_rights_templates($condition = null)
    {
        return RightsDataManager :: get_instance()->count_rights_templates($condition);
    }

    function count_type_templates($condition = null)
    {
        return RightsDataManager :: get_instance()->count_type_templates($condition);
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

    function retrieve_type_template($id)
    {
        return RightsDataManager :: get_instance()->retrieve_type_template($id);
    }

    function retrieve_location($id)
    {
        return RightsDataManager :: get_instance()->retrieve_location($id);
    }

    function retrieve_right($id)
    {
        return RightsDataManager :: get_instance()->retrieve_right($id);
    }

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('Locations'), Translation :: get('LocationsDescription'), Theme :: get_image_path() . 'browse_location.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_MANAGE_LOCATIONS), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('TypeTemplates'), Translation :: get('TypeTemplatesDescription'), Theme :: get_image_path() . 'browse_template.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_MANAGE_TYPE_TEMPLATES), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('RightsTemplates'), Translation :: get('RightsTemplatesDescription'), Theme :: get_image_path() . 'browse_template.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_MANAGE_RIGHTS_TEMPLATES), array(), false, Redirect :: TYPE_CORE));

        $links[] = new DynamicAction(Translation :: get('RightsTemplatePermissions'), Translation :: get('RightsTemplatePermissionsDescription'), Theme :: get_image_path() . 'browse_permission_template.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_LOCATION_RIGHTS_TEMPLATES), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('UserPermissions'), Translation :: get('UserPermissionsDescription'), Theme :: get_image_path() . 'browse_permission_user.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_LOCATION_USER_RIGHTS), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('GroupPermissions'), Translation :: get('GroupPermissionsDescription'), Theme :: get_image_path() . 'browse_permission_group.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_BROWSE_LOCATION_GROUP_RIGHTS), array(), false, Redirect :: TYPE_CORE));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
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