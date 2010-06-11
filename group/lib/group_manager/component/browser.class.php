<?php
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
    private $ab;
    private $group;
    private $root_group;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupList')));
        $trail->add_help('group general');

        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $this->ab = $this->get_action_bar();

        $menu = $this->get_menu_html();
        $output = $this->get_user_html();

        $this->display_header();
        echo $this->ab->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_user_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
        $table = new GroupBrowserTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';

        $html[] = '<a name="top"></a>';
        $html[] = '<div id="admin_tabs">';
        $html[] = '<ul style="display: none;">';

        // BEGIN TABS


        $subgroup_count = $this->count_groups($this->get_condition());
        $user_condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $this->get_group());
        $user_count = $this->count_group_rel_users($user_condition);

        if ($subgroup_count > 0)
        {
            $html[] = '<li><a href="#admin_tabs-1">';
            $html[] = '<span class="category">';
            $html[] = '<img src="' . Theme :: get_image_path('admin') . 'place_mini_group.png" border="0" style="vertical-align: middle;" alt="User" title="User"/>';
            $html[] = '<span class="title">Groups</span>';
            $html[] = '</span>';
            $html[] = '</a></li>';
        }

        if ($user_count > 0)
        {
            $html[] = '<li><a href="#admin_tabs-2">';
            $html[] = '<span class="category">';
            $html[] = '<img src="' . Theme :: get_image_path('admin') . 'place_mini_user.png" border="0" style="vertical-align: middle;" alt="User" title="User"/>';
            $html[] = '<span class="title">Users</span>';
            $html[] = '</span>';
            $html[] = '</a></li>';
        }

        $html[] = '</ul>';

        // Groups
        if ($subgroup_count > 0)
        {
            $html[] = '<h2><img src="' . Theme :: get_image_path('admin') . 'place_mini_group.png" border="0" style="vertical-align: middle;" alt="User" title="User"/>&nbsp;Subgroups</h2>';
            $html[] = '<div class="admin_tab" style="padding: 15px;" id="admin_tabs-1">';
            $html[] = '<a class="prev"></a>';
            $html[] = $table->as_html();
            $html[] = '<a class="next"></a>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        // Users
        if ($user_count > 0)
        {
            $html[] = '<h2><img src="' . Theme :: get_image_path('admin') . 'place_mini_user.png" border="0" style="vertical-align: middle;" alt="User" title="User"/>&nbsp;Users</h2>';
            $html[] = '<div class="admin_tab" style="padding: 15px;" id="admin_tabs-2">';
            $html[] = '<a class="prev"></a>';
            $table = new GroupRelUserBrowserTable($this, array(), $user_condition);
            $html[] = $table->as_html();
            $html[] = '<a class="next"></a>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        // END TABS


        $html[] = '</div>';

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

    function get_condition()
    {
        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_group());

        $query = $this->ab->get_query();
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

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $this->get_group())));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_group_url($this->get_group()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewRoot'), Theme :: get_common_image_path() . 'action_home.png', $this->get_group_viewing_url($this->get_root_group()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $this->get_group())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
?>