<?php
/**
 * $Id: user.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.user_right_manager.component
 */
require_once Path :: get_rights_path() . 'lib/user_right_manager/component/location_user_browser_table/location_user_browser_table.class.php';

class UserRightManagerUserComponent extends UserRightManagerComponent
{
    private $action_bar;
    
    private $application;
    private $location;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->application = Request :: get(UserRightManager :: PARAM_SOURCE);
        $location = Request :: get(UserRightManager :: PARAM_LOCATION);
        $user = Request :: get(UserRightManager :: PARAM_USER);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => RightsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Rights')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS)), Translation :: get('UserRights')));
        
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
        $application_url = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_SOURCE => Application :: PLACEHOLDER_APPLICATION));
        $html[] = Application :: get_selecter($application_url, $this->application);
        $html[] = $this->action_bar->as_html() . '<br />';
        
        $url_format = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_LOCATION_USER_RIGHTS, UserRightManager :: PARAM_SOURCE => $this->application, UserRightManager :: PARAM_LOCATION => '%s'));
        $url_format = str_replace('=%25s', '=%s', $url_format);
        $location_menu = new LocationRightMenu($root->get_id(), $this->location->get_id(), $url_format);
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $location_menu->render_as_tree();
        $html[] = '</div>';
        
        $params = array(GroupRightManager :: PARAM_SOURCE => $this->application, GroupRightManager :: PARAM_LOCATION => $this->location->get_id());
        $table = new LocationUserBrowserTable($this, array_merge($this->get_parameters(), $params), $this->get_condition());
        
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = RightsUtilities :: get_rights_legend();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'rights/javascript/configure_user.js');
        
        echo implode("\n", $html);
        
        $this->display_footer();
    }

    function get_condition()
    {
        //return null;
        

        //$condition = new EqualityCondition(Location :: PROPERTY_PARENT, $this->location->get_id());
        

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*', User :: get_table_name());
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*', User :: get_table_name());
            $or_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*', User :: get_table_name());
            $condition = new OrCondition($or_conditions);
        }
        
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
        $action_bar->set_search_url($this->get_url(array(UserRightManager :: PARAM_SOURCE => $this->application, UserRightManager :: PARAM_LOCATION => $this->location->get_id())));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(GroupRightManager :: PARAM_SOURCE => $this->application, GroupRightManager :: PARAM_LOCATION => $this->location->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>