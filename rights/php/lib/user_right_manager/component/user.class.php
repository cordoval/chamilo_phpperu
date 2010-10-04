<?php
/**
 * $Id: user.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.user_right_manager.component
 */
//require_once Path :: get_rights_path() . 'lib/user_right_manager/component/location_user_browser_table/location_user_browser_table.class.php';


class UserRightManagerUserComponent extends UserRightManager
{    
    private $application;
    private $location;
    private $root;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->application = Request :: get(UserRightManager :: PARAM_SOURCE);
        $location = Request :: get(UserRightManager :: PARAM_LOCATION);
        $user = Request :: get(UserRightManager :: PARAM_USER);
        
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
        
        /*$parents = array_reverse($this->location->get_parents()->as_array());
        foreach ($parents as $parent)
        {
            $trail->add(new Breadcrumb($this->get_url(array('location' => $parent->get_id())), $parent->get_location()));
        }*/
        
        $manager = new RightsEditorManager($this, array($this->location));
        $manager->set_modus(RightsEditorManager :: MODUS_USERS);
        $manager->set_parameter(UserRightManager :: PARAM_LOCATION, $this->location->get_id());
        $manager->run();
    }

    function display_header()
    {
        parent :: display_header();
        
        $html = array();
        $application_url = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_SOURCE => Application :: PLACEHOLDER_APPLICATION));
        $html[] = BasicApplication :: get_selecter($application_url, $this->application);
        
        $url_format = $this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_LOCATION_USER_RIGHTS, UserRightManager :: PARAM_SOURCE => $this->application, UserRightManager :: PARAM_LOCATION => '%s'));
        $url_format = str_replace('=%25s', '=%s', $url_format);
        $location_menu = new LocationRightMenu($this->root->get_id(), $this->location->get_id(), $url_format);
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $location_menu->render_as_tree();
        $html[] = '</div>';
        $html[] = '<div style="float: right; width: 80%;">';
        echo implode("\n", $html);
    }

    function display_footer()
    {
        $html = array();
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
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('rights_users_user');
    }
    
	function get_additional_parameters()
    {
    	return array(UserRightManager :: PARAM_SOURCE, UserRightManager :: PARAM_LOCATION, UserRightManager :: PARAM_USER);
    }
}
?>