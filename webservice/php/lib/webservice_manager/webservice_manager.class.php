<?php
/**
 * $Id: webservice_manager.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib.webservice_manager
 */

/**
 * A webservice manager provides some functionalities to the admin to manage
 * his webservices.
 */
class WebserviceManager extends CoreApplication
{

    const APPLICATION_NAME = 'webservice';

    const PARAM_REMOVE_SELECTED = 'delete';
    const PARAM_FIRSTLETTER = 'firstletter';
    const PARAM_COMPONENT_ACTION = 'action';
    const PARAM_SOURCE = 'source';

    const PARAM_LOCATION_ID = 'location';
    const PARAM_WEBSERVICE_ID = 'webservice';
    const PARAM_WEBSERVICE_CATEGORY_ID = 'webservice_category_id';

    const ACTION_BROWSE_WEBSERVICES = 'webservice_browser';
    const ACTION_MANAGE_ROLES = 'rights_editor';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_WEBSERVICES;

    private $parameters;
    private $search_parameters;
    private $user;
    private $breadcrumbs;
    private $instance;

    public function WebserviceManager($user = null)
    {
        parent :: __construct($user);
    }

    public function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    function retrieve_webservices($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return WebserviceDataManager :: get_instance()->retrieve_webservices($condition, $offset, $count, $order_property);
    }

    function count_webservices($condition = null)
    {
        return WebserviceDataManager :: get_instance()->count_webservices($condition);
    }

    function retrieve_webservice_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return WebserviceDataManager :: get_instance()->retrieve_webservice_categories($condition, $offset, $count, $order_property);
    }

    function retrieve_webservice($id)
    {
        return WebserviceDataManager :: get_instance()->retrieve_webservice($id);
    }

    function retrieve_webservice_by_name($name)
    {
        return WebserviceDataManager :: get_instance()->retrieve_webservice_by_name($name);
    }

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('List'), Translation :: get('ListDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_WEBSERVICES), array(), false, Redirect :: TYPE_CORE));
        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;

        return $info;
    }

    public function get_manage_roles_url($webservice)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_WEBSERVICE_ID => $webservice->get_id()));
    }

    public function get_manage_roles_cat_url($webserviceCategory)
    {
        if (! $webserviceCategory)
            $webserviceCategory = 0;
        else
            $webserviceCategory = $webserviceCategory->get_id();

        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_WEBSERVICE_CATEGORY_ID => $webserviceCategory));
    }

    public function get_tool_bar_item($id)
    {
        $toolbar_item = WebserviceDataManager :: get_instance()->retrieve_webservice_category($id);
        if (isset($toolbar_item))
        {
            $url = $this->get_manage_roles_cat_url($toolbar_item);
        }
        else
        {
            $url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_WEBSERVICE_CATEGORY_ID => null));
        }
        return new ToolbarItem(Translation :: get('ChangeRights'), Theme :: get_common_image_path() . 'action_rights.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false);
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