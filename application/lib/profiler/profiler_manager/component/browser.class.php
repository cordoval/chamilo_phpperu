<?php
/**
 * $Id: browser.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
require_once dirname(__FILE__) . '/../profiler_manager.class.php';
require_once dirname(__FILE__) . '/profile_publication_browser/profile_publication_browser_table.class.php';
require_once dirname(__FILE__) . '/../../profiler_menu.class.php';

class ProfilerManagerBrowserComponent extends ProfilerManager
{
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $menu = new ProfilerMenu($this->get_category());
        
        $this->action_bar = $this->get_action_bar();
        
        $output = $this->get_publications_html();
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyProfiler')));
        $trail->merge($menu->get_breadcrumbs());
        $trail->add_help('profiler general');
        
        $this->display_header($trail);
        
        echo $this->action_bar->as_html();
        echo '<div class="clear"></div>';
        
        echo '<div style="width: 12%; overflow: auto; float: left;">';
        echo $this->get_menu();
        echo '</div><div style="width: 85%; float: right;">';
        echo $output;
        echo '</div>';
        
        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url(array('category' => $this->get_category())));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_profile_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => $this->get_category())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_profiler_category_manager_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function get_menu()
    {
        $menu = new ProfilerMenu($this->get_category());
        return $menu->render_as_tree();
    }

    function get_category()
    {
        return Request :: get('category') ? Request :: get('category') : 0;
    }

    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);
        
        $table = new ProfilePublicationBrowserTable($this, null, $parameters, $this->get_condition());
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        /*$search_conditions = $this->get_search_condition();
		//$search_conditions = null;
		$condition = null;
		if (isset($this->firstletter))
		{
			$conditions = array();
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, $this->firstletter. '*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+1). '*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+2). '*');
			$condition = new OrCondition($conditions);
			if (count($search_conditions))
			{
				$condition = new AndCondition($condition, $search_conditions);
			}
		}
		else
		{
			if (count($search_conditions))
			{
				$condition = $search_conditions;
			}
		}

		return $condition;*/
        $condition = new EqualityCondition(ProfilePublication :: PROPERTY_CATEGORY, $this->get_category());
        $search = $this->action_bar->get_query();
        
        if (isset($search) && $search != '')
        {
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $search . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $search . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $search . '*');
            $or_condition = new OrCondition($conditions);
            
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = $or_condition;
            
            $condition = new AndCondition($conditions);
        }
        
        return $condition;
    }
}
?>