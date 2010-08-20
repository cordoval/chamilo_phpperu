<?php
require_once dirname(__FILE__) . "/../../group_rights.class.php";

/**
 * $Id: browser.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */

require_once dirname(__FILE__) . '/../../group_tree_menu_data_provider.class.php';

class GroupManagerBrowserComponent extends GroupManager
{
    const TAB_SUBGROUPS = 0;
    const TAB_USERS = 1;
    const TAB_DETAILS = 2;

    private $action_bar;
    private $group;
    private $root_group;
    private $edit_right;
    private $view_right;
    private $create_right;
    private $export_right;
    private $move_right;
    private $subscribe_right;
    private $unsubscribe_right;
    private $delete_right;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupList')));
        $trail->add_help('group general');

        $this->edit_right = GroupRights :: is_allowed_in_groups_subtree(GroupRights :: RIGHT_EDIT, $this->get_group());
        $this->view_right = GroupRights :: is_allowed_in_groups_subtree(GroupRights :: RIGHT_VIEW, $this->get_group());
        $this->create_right = GroupRights :: is_allowed_in_groups_subtree(GroupRights :: RIGHT_CREATE, $this->get_group());
        $this->export_right = GroupRights :: is_allowed_in_groups_subtree(GroupRights :: RIGHT_EXPORT, $this->get_group());
        $this->move_right = GroupRights :: is_allowed_in_groups_subtree(GroupRights :: RIGHT_MOVE, $this->get_group());
        $this->subscribe_right = GroupRights :: is_allowed_in_groups_subtree(GroupRights :: RIGHT_SUBSCRIBE, $this->get_group());
        $this->unsubscribe_right = GroupRights :: is_allowed_in_groups_subtree(GroupRights :: RIGHT_UNSUBSCRIBE, $this->get_group());
        $this->delete_right = GroupRights :: is_allowed_in_groups_subtree(GroupRights :: RIGHT_DELETE, $this->get_group());

        if (! ($this->edit_right || $this->view_right || $this->export_right || $this->subscribe_right || $this->unsubscribe_right || $this->delete_right || $this->move_right || $this->create_right))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $this->action_bar = $this->get_action_bar();

        $menu = $this->get_menu_html();
        $output = $this->get_user_html();

        $this->display_header();
        echo $this->action_bar->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_user_html()
    {
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';

        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);

        $subgroup_count = $this->count_groups($this->get_subgroups_condition());
        $user_count = $this->count_group_rel_users($this->get_users_condition());

        // Subgroups table tab
        //        if ($subgroup_count > 0)
        //        {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();

        $table = new GroupBrowserTable($this, $parameters, $this->get_subgroups_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_SUBGROUPS, Translation :: get('Subgroups'), Theme :: get_image_path('admin') . 'place_mini_group.png', $table->as_html()));
        //        }


        // Users table tab
        //        if ($user_count > 0)
        //        {
        $parameters = $this->get_parameters();
        $parameters[GroupManager :: PARAM_GROUP_ID] = $id;

        $table = new GroupRelUserBrowserTable($this, $parameters, $this->get_users_condition());
        $tabs->add_tab(new DynamicContentTab(self :: TAB_USERS, Translation :: get('Users'), Theme :: get_image_path('admin') . 'place_mini_user.png', $table->as_html()));
        //        }


        // Group info tab
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAILS, Translation :: get('Details'), Theme :: get_image_path('admin') . 'place_mini_help.png', $this->get_group_info()));

        $html[] = $tabs->render();

        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';

        return implode($html, "\n");
    }

    function get_menu_html()
    {
        //$group_menu = new GroupMenu($this->get_group());
        $group_menu = new TreeMenu('GroupTreeMenu', new GroupTreeMenuDataProvider($this->get_url(), $this->get_group()));
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $group_menu->render_as_tree();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_group()
    {
        if (! $this->group)
        {
            $this->group = Request :: get(GroupManager :: PARAM_GROUP_ID);

            if (! $this->group)
            {
                $this->group = $this->get_root_group()->get_id();
            }

        }

        return $this->group;
    }

    function get_root_group()
    {
        if (! $this->root_group)
        {
            $group = $this->retrieve_groups(new EqualityCondition(Group :: PROPERTY_PARENT, 0))->next_result();
            $this->root_group = $group;
        }

        return $this->root_group;
    }

    function get_subgroups_condition()
    {
        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_group());

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(Group :: PROPERTY_NAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(Group :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(Group :: PROPERTY_CODE, '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);

            $and_conditions = array();
            $and_conditions[] = $condition;
            $and_conditions[] = $or_condition;
            $condition = new AndCondition($and_conditions);
        }

        return $condition;
    }

    function get_users_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $this->get_group());

        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $condition = new OrCondition($or_conditions);

            $users = UserDataManager :: get_instance()->retrieve_users($condition);
            while ($user = $users->next_result())
            {
                $userconditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user->get_id());
            }

            if (count($userconditions))
            {
                $conditions[] = new OrCondition($userconditions);
            }
            else
            {
                $conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, 0);
            }

        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $this->get_group())));

        if ($this->create_right)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_group_url($this->get_group()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewRoot'), Theme :: get_common_image_path() . 'action_home.png', $this->get_group_viewing_url($this->get_root_group()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $this->get_group())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    function get_group_info()
    {
        $group_id = $this->get_group();
        $group = $this->retrieve_group($group_id);

        $html = array();

        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        //        $action_bar->set_search_url($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group->get_id())));


        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_group_editing_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->group != $this->root_group)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_group_delete_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $toolbar->add_item(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->get_group_suscribe_user_browser_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(new ToolbarItem(Translation :: get('ManageRightsTemplates'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_manage_group_rights_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
        $users = $this->retrieve_group_rel_users($condition);
        $visible = ($users->size() > 0);

        if ($visible)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $this->get_group_emptying_url($group), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $html[] = '<b>' . Translation :: get('Code') . '</b>: ' . $group->get_code() . '<br />';

        $description = $group->get_description();
        if ($description)
        {
            $html[] = '<b>' . Translation :: get('Description') . '</b>: ' . $description . '<br />';
        }

        $html[] = '<br />';
        $html[] = $toolbar->as_html();

        return implode("\n", $html);
    }
}
?>