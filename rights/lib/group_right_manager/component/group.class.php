<?php
/**
 * $Id: group.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.group_right_manager.component
 */
require_once Path :: get_rights_path() . 'lib/group_right_manager/component/location_group_browser_table/location_group_browser_table.class.php';

class GroupRightManagerGroupComponent extends GroupRightManager
{
    private $action_bar;
    
    private $application;
    private $location;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->application = Request :: get(GroupRightManager :: PARAM_SOURCE);
        $location = Request :: get(GroupRightManager :: PARAM_LOCATION);
        $group = Request :: get(GroupRightManager :: PARAM_GROUP);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => RightsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Rights')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS)), Translation :: get('GroupRights')));
        
        if (! isset($this->application))
        {
            $this->application = 'admin';
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, 0);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->application);
        $conditions[] = new EqualityCondition(Location :: PROPERTY_TREE_TYPE, 'root');
        $condition = new AndCondition($conditions);
        $root = RightsDataManager :: get_instance()->retrieve_locations($condition, null, 1, array(new ObjectTableOrder(Location :: PROPERTY_LOCATION)))->next_result();
        
        if (isset($location))
        {
            $this->location = $this->retrieve_location($location);
        }
        else
        {
            $this->location = $root;
        }
        
        $parents = array_reverse($this->location->get_parents()->as_array());
        foreach ($parents as $parent)
        {
            $trail->add(new Breadcrumb($this->get_url(array('location' => $parent->get_id())), $parent->get_location()));
        }
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header($trail);
        
        $html = array();
        $application_url = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_SOURCE => Application :: PLACEHOLDER_APPLICATION, GroupRightManager :: PARAM_GROUP => Request :: get(GroupRightManager :: PARAM_GROUP)));
        $html[] = Application :: get_selecter($application_url, $this->application);
        $html[] = $this->action_bar->as_html() . '<br />';
        
        $url_format = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_BROWSE_LOCATION_GROUP_RIGHTS, GroupRightManager :: PARAM_SOURCE => $this->application, GroupRightManager :: PARAM_GROUP => Request :: get(GroupRightManager :: PARAM_GROUP), GroupRightManager :: PARAM_LOCATION => '%s'));
        $url_format = str_replace('=%25s', '=%s', $url_format);
        $location_menu = new LocationRightMenu($root->get_id(), $this->location->get_id(), $url_format);
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $location_menu->render_as_tree();
        $html[] = '</div>';
        
        $params = array(GroupRightManager :: PARAM_SOURCE => $this->application, GroupRightManager :: PARAM_LOCATION => $this->location->get_id(), GroupRightManager :: PARAM_GROUP => Request :: get(GroupRightManager :: PARAM_GROUP));
        $html[] = '<div style="float: left; width: 62%; margin-left: 1%;">';
        $table = new LocationGroupBrowserTable($this, array_merge($this->get_parameters(), $params), $this->get_condition());
        $html[] = $table->as_html();
        $html[] = RightsUtilities :: get_rights_legend();
        $html[] = '</div>';
        
        $html[] = '<div style="float: right; width: 18%; overflow: auto; height: 500px;">';
        $group_menu = new GroupMenu($group, 'core.php?go=group&action=group&application=rights&group=%s');
        $html[] = $group_menu->render_as_tree();
        $html[] = '</div>';
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'rights/javascript/configure_group.js');
        
        echo implode("\n", $html);
        
        $this->display_footer();
    }

    function get_condition()
    {
        $group = Request :: get(GroupRightManager :: PARAM_GROUP) ? Request :: get(GroupRightManager :: PARAM_GROUP) : 0;
        $conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, $group);
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(Group :: PROPERTY_NAME, '*' . $query . '*');
        
        }
        
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    function get_source()
    {
        return $this->application;
    }

    function get_location()
    {
        return $this->location;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(GroupRightManager :: PARAM_SOURCE => $this->application, GroupRightManager :: PARAM_LOCATION => $this->location->get_id(), GroupRightManager :: PARAM_GROUP => Request :: get(GroupRightManager :: PARAM_GROUP))));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(GroupRightManager :: PARAM_SOURCE => $this->application, GroupRightManager :: PARAM_LOCATION => $this->location->get_id(), GroupRightManager :: PARAM_GROUP => Request :: get(GroupRightManager :: PARAM_GROUP))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>