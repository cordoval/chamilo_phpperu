<?php
require_once dirname ( __FILE__ ) . '/../period_manager.class.php';
require_once dirname ( __FILE__ ) . '/browser/browser_table.class.php';

class InternshipOrganizerPeriodManagerBrowserComponent extends InternshipOrganizerPeriodManager 
{
	private $ab;
	private $period;
	private $root_period;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run() 
	{
		
		$trail = new BreadcrumbTrail ();
		
		$trail->add ( new Breadcrumb ( $this->get_url (), Translation::get ( 'BrowseInternshipOrganizerPeriods' ) ) );
		$trail->add_help ( 'period general' );
		
		$this->ab = $this->get_action_bar ();
		
		$menu = $this->get_menu_html ();
		$output = $this->get_browser_html ();
		
		$this->display_header ( $trail );
		echo $this->ab->as_html () . '<br />';
		echo $menu;
		echo $output;
		$this->display_footer ();
	}
	
	function get_browser_html() 
	{
		$table = new InternshipOrganizerPeriodBrowserTable ( $this, $this->get_parameters (), $this->get_condition () );
		
		$html = array ();
		$html [] = '<div style="float: right; width: 80%;">';
		$html [] = $table->as_html ();
		$html [] = '</div>';
		$html [] = '<div class="clear"></div>';
		
		return implode ( $html, "\n" );
	}
	
	function get_menu_html() 
	{
		$period_menu = new InternshipOrganizerPeriodMenu ( $this->get_period () );
		$html = array ();
		$html [] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
		$html [] = $period_menu->render_as_tree ();
		$html [] = '</div>';
		
		return implode ( $html, "\n" );
	}
	
	function get_period() 
	{
		if (! $this->period) 
		{
			$period_id = Request::get ( InternshipOrganizerPeriodManager::PARAM_PERIOD_ID );
			
			if (! $period_id) 
			{
				$this->period = $this->get_root_period()->get_id ();
			}else
			{
				$this->period = $period_id;
			}
		
		}
		
		return $this->period;
	}
	
	function get_root_period() 
	{
		if (! $this->root_period) 
		{
			$this->root_period = $this->retrieve_root_period ();
		}
		
		return $this->root_period;
	}
	
	function get_condition() 
	{
		$condition = new EqualityCondition ( InternshipOrganizerPeriod::PROPERTY_PARENT_ID, $this->get_period () );
		
		$query = $this->ab->get_query ();
		if (isset ( $query ) && $query != '')
		{
			$or_conditions = array ();
			$or_conditions [] = new PatternMatchCondition ( InternshipOrganizerPeriod::PROPERTY_NAME, '*' . $query . '*' );
			$or_conditions [] = new PatternMatchCondition ( InternshipOrganizerPeriod::PROPERTY_DESCRIPTION, '*' . $query . '*' );
			$or_condition = new OrCondition ( $or_conditions );
			
			$and_conditions = array ();
			$and_conditions [] = $condition;
			$and_conditions [] = $or_condition;
			$condition = new AndCondition ( $and_conditions );
		}
		
		return $condition;
	}
	
	function get_action_bar() 
	{
		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );
		
		$action_bar->set_search_url ( $this->get_url ( array (InternshipOrganizerPeriodManager::PARAM_PERIOD_ID => $this->get_period () ) ) );
		
		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'CreateInternshipOrganizerPeriod' ), Theme::get_common_image_path () . 'action_add.png', $this->get_period_create_url ( $this->get_period () ), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ViewRoot' ), Theme::get_common_image_path () . 'action_home.png', $this->get_browse_periods_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ShowAll' ), Theme::get_common_image_path () . 'action_browser.png', $this->get_browse_periods_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
		
		return $action_bar;
	}
}
?>