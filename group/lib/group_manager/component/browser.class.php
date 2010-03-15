<?php
/**
 * $Id: browser.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class GroupManagerBrowserComponent extends GroupManagerComponent
{
    private $ab;
    private $group;
    private $root_group;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupList')));
        $trail->add_help('group general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $this->ab = $this->get_action_bar();
        
        $menu = $this->get_menu_html();
        $output = $this->get_user_html();
        
        $this->display_header($trail);
        echo $this->ab->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_user_html()
    {
        $table = new GroupBrowserTable($this, $this->get_parameters(), $this->get_condition());
        
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_menu_html()
    {
        $group_menu = new GroupMenu($this->get_group());
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $group_menu->render_as_tree();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

    function get_group()
    {
        if(!$this->group)
        {
    		$this->group = Request :: get(GroupManager :: PARAM_GROUP_ID);
    		
    		if(!$this->group)
    		{
    			$this->group = $this->get_root_group()->get_id();
    		}
    		
        }
        
        return $this->group;
    }
    
    function get_root_group()
    {
    	if(!$this->root_group)
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
            $or_conditions[] = new LikeCondition(Group :: PROPERTY_NAME, $query);
            $or_conditions[] = new LikeCondition(Group :: PROPERTY_DESCRIPTION, $query);
            $or_conditions[] = new LikeCondition(Group :: PROPERTY_CODE, $query);
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