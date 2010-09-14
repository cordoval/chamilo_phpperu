<?php
class MenuManagerBrowserComponent extends MenuManager implements AdministrationComponent
{
	private $action_bar;
	
	function run()
	{
	    $this->check_allowed();
		$this->show_navigation_item_list();	
	}
	
	function show_navigation_item_list()
    {
        $this->action_bar = $this->get_action_bar();
		$this->category = Request :: get(self :: PARAM_ITEM);
        
        $parameters = $this->get_parameters(true);
		$parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
		
        $table = new NavigationItemBrowserTable($this, $parameters, $this->get_condition());

        $this->display_header();

        echo $this->action_bar->as_html();

        echo '<div style="float: left; width: 12%; overflow:auto;">';
        echo $this->get_menu()->render_as_tree();
        echo '</div>';
        echo '<div style="float: right; width: 85%;">';
        echo $table->as_html();
        echo '</div>';
        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $category = (isset($this->category) ? $this->category : 0);
        $action_bar->set_search_url($this->get_url(array(self :: PARAM_ITEM => $category)));

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddItem'), Theme :: get_common_image_path() . 'action_create.png', $this->get_navigation_item_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddCategory'), Theme :: get_common_image_path() . 'action_category.png', $this->get_category_navigation_item_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(self :: PARAM_ITEM => $category)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
    
    function get_condition()
    {
        $condition = null;
        $category = (isset($this->category) ? $this->category : 0);
        $condition = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, $category);

        $search = $this->action_bar->get_query();
        if (isset($search) && $search != '')
        {
            $conditions[] = $condition;
            $conditions[] = new PatternMatchCondition(NavigationItem :: PROPERTY_TITLE, '*' . $search . '*');
            $condition = new AndCondition($conditions);
        }

        return $condition;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('menu_browser');
    }
    
    function get_additional_parameters()
    {
    	return array(MenuManager :: PARAM_ITEM);
    }
}

?>