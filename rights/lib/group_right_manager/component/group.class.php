<?php
/**
 * $Id: group.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.group_right_manager.component
 */
//require_once Path :: get_rights_path() . 'lib/group_right_manager/component/location_group_browser_table/location_group_browser_table.class.php';

class GroupRightManagerGroupComponent extends GroupRightManager
{    
    private $application;
    private $location;
    private $root;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->application = Request :: get(GroupRightManager :: PARAM_SOURCE);
        $location = Request :: get(GroupRightManager :: PARAM_LOCATION);
        $group = Request :: get(RightsEditorManager :: PARAM_GROUP);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => RightsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Rights')));
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
        $this->root = RightsDataManager :: get_instance()->retrieve_locations($condition, null, 1, array(new ObjectTableOrder(Location :: PROPERTY_LOCATION)))->next_result();
        
        if (isset($location))
        {
            $this->location = $this->retrieve_location($location);
        }
        else
        {
            $this->location = $this->root;
        }
        
        $parents = array_reverse($this->location->get_parents()->as_array());
        foreach ($parents as $parent)
        {
            $trail->add(new Breadcrumb($this->get_url(array('location' => $parent->get_id())), $parent->get_location()));
        }
        
        $manager = new RightsEditorManager($this, array($this->location));
        $manager->set_modus(RightsEditorManager :: MODUS_GROUPS);
        $manager->set_parameter(GroupRightManager :: PARAM_LOCATION, $this->location->get_id());
        $manager->run();
    }
    
    function display_header()
    {
        parent :: display_header();
        
        $html = array();
        $application_url = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_SOURCE => Application :: PLACEHOLDER_APPLICATION, GroupRightManager :: PARAM_GROUP => Request :: get(GroupRightManager :: PARAM_GROUP)));
        $html[] = BasicApplication :: get_selecter($application_url, $this->application);
        $html[] = '<div style="float: left; width: 80%; margin-right: 1%;">';
        echo implode("\n", $html);
    }
    
    function display_footer()
    {
        $html = array();
        $html[] = '</div>';
        
        $url_format = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS, GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_BROWSE_LOCATION_GROUP_RIGHTS, GroupRightManager :: PARAM_SOURCE => $this->application, RightsEditorManager :: PARAM_GROUP => Request :: get(RightsEditorManager :: PARAM_GROUP), GroupRightManager :: PARAM_LOCATION => '%s'));
        $url_format = str_replace('=%25s', '=%s', $url_format);
        $location_menu = new LocationRightMenu($this->root->get_id(), $this->location->get_id(), $url_format);
        
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $location_menu->render_as_tree();
        $html[] = '</div>';
        echo implode("\n", $html);
        
        parent :: display_footer();
    }

    function get_source()
    {
        return $this->application;
    }

    function get_location()
    {
        return $this->location;
    }
    
    function get_available_rights()
    {
        return RightsUtilities :: get_available_rights($this->get_source());
    }
}
?>