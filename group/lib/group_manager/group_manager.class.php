<?php
/**
 * $Id: group_manager.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class GroupManager extends CoreApplication
{
    const APPLICATION_NAME = 'group';

    const PARAM_GROUP_ID = 'group_id';
    const PARAM_GROUP_REL_USER_ID = 'group_rel_user_id';
    const PARAM_USER_ID = 'user_id';
    const PARAM_FIRSTLETTER = 'firstletter';
    const PARAM_COMPONENT_ACTION = 'action';

    const ACTION_CREATE_GROUP = 'create';
    const ACTION_BROWSE_GROUPS = 'browse';
    const ACTION_EDIT_GROUP = 'edit';
    const ACTION_DELETE_GROUP = 'delete';
    const ACTION_MOVE_GROUP = 'move';
    const ACTION_TRUNCATE_GROUP = 'truncate';
    const ACTION_VIEW_GROUP = 'view';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';
    const ACTION_IMPORT_GROUP_USERS = 'import_group_users';
    const ACTION_SUBSCRIBE_USER_TO_GROUP = 'subscribe';
    const ACTION_SUBSCRIBE_USER_BROWSER = 'subscribe_browser';
    const ACTION_UNSUBSCRIBE_USER_FROM_GROUP = 'unsubscribe';
    const ACTION_MANAGE_RIGHTS_TEMPLATES = 'manage_group_rights_templates';
    const ACTION_RIGHT_EDITS = 'edit_group_rights';

    private $parameters;
    private $search_parameters;
    private $user_search_parameters;
    private $search_form;
    private $user_search_form;
    private $user_id;
    private $user;
    private $category_menu;
    private $quota_url;
    private $publication_url;
    private $create_url;
    private $recycle_bin_url;
    private $breadcrumbs;

    function GroupManager($user = null)
    {
        parent :: __construct($user);
        $this->create_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_GROUP));
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
            case self :: ACTION_CREATE_GROUP :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_EDIT_GROUP :
                $component = $this->create_component('Editor');
                break;
            case self :: ACTION_DELETE_GROUP :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_MOVE_GROUP :
                $component = $this->create_component('Mover');
                break;
            case self :: ACTION_TRUNCATE_GROUP :
                $component = $this->create_component('Truncater');
                break;
            case self :: ACTION_VIEW_GROUP :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_EXPORT :
                $component = $this->create_component('Exporter');
                break;
            case self :: ACTION_IMPORT :
                $component = $this->create_component('Importer');
                break;
            case self :: ACTION_RIGHT_EDITS:
                $component = $this->create_component('RightsEditor');
                break;
            case self :: ACTION_IMPORT_GROUP_USERS :
                $component = $this->create_component('GroupUserImporter');
                break;
            case self :: ACTION_BROWSE_GROUPS :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_UNSUBSCRIBE_USER_FROM_GROUP :
                $component = $this->create_component('Unsubscriber');
                break;
            case self :: ACTION_SUBSCRIBE_USER_TO_GROUP :
                $component = $this->create_component('Subscriber');
                break;
            case self :: ACTION_SUBSCRIBE_USER_BROWSER :
                $component = $this->create_component('SubscribeUserBrowser');
                break;
            case self :: ACTION_MANAGE_RIGHTS_TEMPLATES :
                $component = $this->create_component('GroupRightsTemplateManager');
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_GROUPS);
                $component = $this->create_component('Browser');
        }
        $component->run();
    }

    /**
     * Displays the header.
     * @param array $breadcrumbs Breadcrumbs to show in the header.
     * @param boolean $display_search Should the header include a search form or
     * not?
     */
    function display_header($breadcrumbtrail, $display_search = false, $helpitem)
    {
        if (is_null($breadcrumbtrail))
        {
            $breadcrumbtrail = BreadcrumbTrail :: get_instance();
        }

        $categories = $this->breadcrumbs;
        if (count($categories) > 0)
        {
            foreach ($categories as $category)
            {
                $breadcrumbtrail->add(new Breadcrumb($category['url'], $category['title']));
            }
        }

        $title = $breadcrumbtrail->get_last()->get_name();
        $title_short = $title;
        if (strlen($title_short) > 53)
        {
            $title_short = substr($title_short, 0, 50) . '&hellip;';
        }
        Display :: header($breadcrumbtrail);
        echo '<h3 style="float: left;" title="' . $title . '">' . $title_short . '</h3>';
        if ($display_search)
        {
            $this->display_search_form();
        }

        echo '<div class="clear">&nbsp;</div>';
        if ($msg = Request :: get(Application :: PARAM_MESSAGE))
        {
            $this->display_message($msg);
        }
        if ($msg = Request :: get(Application :: PARAM_ERROR_MESSAGE))
        {
            $this->display_error_message($msg);
        }
    }

    private function display_search_form()
    {
        echo $this->get_search_form()->display();
    }

    function display_user_search_form()
    {
        echo $this->get_user_search_form()->display();
    }

    function count_groups($condition = null)
    {
        return GroupDataManager :: get_instance()->count_groups($condition);
    }

    function count_group_rel_users($condition = null)
    {
        return GroupDataManager :: get_instance()->count_group_rel_users($condition);
    }

    /**
     * Displays the footer.
     */
    function display_footer()
    {
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        Display :: footer();
    }

    function get_search_condition()
    {
        return $this->get_search_form()->get_condition();
    }

    function get_user_search_condition()
    {
        return $this->get_user_search_form()->get_condition();
    }

    private function get_search_form()
    {
        if (! isset($this->search_form))
        {
            $this->search_form = new GroupSearchForm($this, $this->get_url());
        }
        return $this->search_form;
    }

    private function get_user_search_form()
    {
        if (! isset($this->user_search_form))
        {
            $this->user_search_form = new UserSearchForm($this, $this->get_url(array(self :: PARAM_GROUP_ID => Request :: get(self :: PARAM_GROUP_ID))));
        }
        return $this->user_search_form;
    }

    function get_search_validate()
    {
        return $this->get_search_form()->validate();
    }

    function get_user_search_validate()
    {
        return $this->get_user_search_form()->validate();
    }

    /**
     * Gets the parameter list
     * @param boolean $include_search Include the search parameters in the
     * returned list?
     * @return array The list of parameters.
     */
    function get_parameters($include_search = false, $include_user_search = false)
    {
        $parms = parent :: get_parameters();

        if ($include_search && isset($this->search_parameters))
        {
            $parms = array_merge($this->search_parameters, $parms);
        }

        if ($include_user_search && isset($this->user_search_parameters))
        {
            $parms = array_merge($this->user_search_parameters, $parms);
        }

        return $parms;
    }

    function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return GroupDataManager :: get_instance()->retrieve_groups($condition, $offset, $count, $order_property);
    }

    function retrieve_group_rel_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return GroupDataManager :: get_instance()->retrieve_group_rel_users($condition, $offset, $count, $order_property);
    }

    function retrieve_group_rel_user($user_id, $group_id)
    {
        return GroupDataManager :: get_instance()->retrieve_group_rel_user($user_id, $group_id);
    }

    /**
     * Sets the active URL in the navigation menu.
     * @param string $url The active URL.
     */
    function force_menu_url($url)
    {
        //$this->get_category_menu()->forceCurrentUrl($url);
    }

    function retrieve_group($id)
    {
        $gdm = GroupDataManager :: get_instance();
        return $gdm->retrieve_group($id);
    }

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('List'), Translation :: get('ListDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('Create'), Translation :: get('CreateDescription'), Theme :: get_image_path() . 'browse_add.png', Redirect :: get_link(array(Application :: PARAM_ACTION => GroupManager :: ACTION_CREATE_GROUP, GroupManager :: PARAM_GROUP_ID => 0), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('Export'), Translation :: get('ExportDescription'), Theme :: get_image_path() . 'browse_export.png', Redirect :: get_link(array(Application :: PARAM_ACTION => GroupManager :: ACTION_EXPORT), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('Import'), Translation :: get('ImportDescription'), Theme :: get_image_path() . 'browse_import.png', Redirect :: get_link(array(Application :: PARAM_ACTION => GroupManager :: ACTION_IMPORT), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('ImportGroupUsers'), Translation :: get('ImportGroupUsersDescription'), Theme :: get_image_path() . 'browse_import.png', Redirect :: get_link(array(Application :: PARAM_ACTION => GroupManager :: ACTION_IMPORT_GROUP_USERS), array(), false, Redirect :: TYPE_CORE));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;
        $info['search'] = Redirect :: get_link(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS), array(), false, Redirect :: TYPE_CORE);

        return $info;
    }

    function get_group_editing_url($group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_GROUP, self :: PARAM_GROUP_ID => $group->get_id()));
    }

    function get_create_group_url($parent_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_GROUP, self :: PARAM_GROUP_ID => $parent_id));
    }

    function get_group_emptying_url($group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TRUNCATE_GROUP, self :: PARAM_GROUP_ID => $group->get_id()));
    }

    function get_group_edit_rights_url($group)
    {
       return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_RIGHT_EDITS, self :: PARAM_GROUP_ID => $group->get_id()));
    }

    function get_group_viewing_url($group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_GROUP, self :: PARAM_GROUP_ID => $group->get_id()));
    }

    function get_group_rel_user_unsubscribing_url($groupreluser)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_USER_FROM_GROUP, self :: PARAM_GROUP_REL_USER_ID => $groupreluser->get_group_id() . '|' . $groupreluser->get_user_id()));
    }

    function get_group_rel_user_subscribing_url($group, $user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_TO_GROUP, self :: PARAM_GROUP_ID => $group->get_id(), self :: PARAM_USER_ID => $user->get_id()));
    }

    function get_group_suscribe_user_browser_url($group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_BROWSER, self :: PARAM_GROUP_ID => $group->get_id()));
    }

    function get_group_delete_url($group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_GROUP, self :: PARAM_GROUP_ID => $group->get_id()));
    }

    function get_import_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT));
    }

    function get_export_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT));
    }

    function get_move_group_url($group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_GROUP, self :: PARAM_GROUP_ID => $group->get_id()));
    }

    function get_manage_group_rights_url($group)
    {
        return $this->get_url(array(Application :: PARAM_APPLICATION => RightsManager :: APPLICATION_NAME, Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_BROWSE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP => $group->get_id()));
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