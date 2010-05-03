<?php

require_once dirname ( __FILE__ ) . '/../survey_builder_component.class.php';
require_once dirname ( __FILE__ ) . '/context_template_browser/browser_table.class.php';
require_once dirname ( __FILE__ ) . '/context_template_menu.class.php';


class SurveyBuilderContextBrowserComponent extends SurveyBuilderComponent 
{
	private $ab;
	private $template;
		
	
	/**
	 * Runs this component and displays its output.
	 */
	function run() 
	{
		
				
		$current_template = Request::get ( SurveyBuilder :: PARAM_TEMPLATE_ID );

		if ($current_template == '0' || is_null ( $current_template )) {
			$template_id =  $this->get_root_lo()->get_context_template_id ();
			$this->template = $template_id;
		} else {
			$this->template = $current_template;
		}
		
		$trail = new BreadcrumbTrail (false);
		
		$trail->add ( new Breadcrumb ( $this->get_url (), Translation::get ('BrowseContext') ) );
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
		$table = new SurveyContextTemplateBrowserTable ( $this, $this->get_parameters (), $this->get_condition () );
		
		$html = array ();
		$html [] = '<div style="float: right; width: 80%;">';
		$html [] = $table->as_html ();
		$html [] = '</div>';
		$html [] = '<div class="clear"></div>';
		
		return implode ( $html, "\n" );
	}
	
	function get_menu_html() 
	{
		$template_menu = new SurveyContextTemplateMenu ($this->template, $this->get_root_lo()->get_id());
		$html = array ();
		$html [] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
		$html [] = $template_menu->render_as_tree ();
		$html [] = '</div>';
		
		return implode ( $html, "\n" );
	}
		
	function get_condition() 
	{
		$condition = new EqualityCondition ( SurveyContextTemplate::PROPERTY_ID, $this->template );
		
		$query = $this->ab->get_query ();
		if (isset ( $query ) && $query != '') 
		{
			$or_conditions = array ();
			$or_conditions [] = new PatternMatchCondition ( SurveyContextTemplate::PROPERTY_NAME, '*' . $query . '*', SurveyContextTemplate :: get_table_name() );
			$or_conditions [] = new PatternMatchCondition ( SurveyContextTemplate::PROPERTY_DESCRIPTION, '*' . $query . '*', SurveyContextTemplate :: get_table_name() );
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
		
		$action_bar->set_search_url ( $this->get_url ( array (SurveyBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), SurveyBuilder::PARAM_TEMPLATE_ID => $this->template ) ) );
		
//		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'Add' ), Theme::get_common_image_path () . 'action_add.png', $this->get_create_category_url ( $this->get_category () ), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
//		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ViewRoot' ), Theme::get_common_image_path () . 'action_home.png', $this->get_browse_categories_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
//		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ShowAll' ), Theme::get_common_image_path () . 'action_browser.png', $this->get_browse_categories_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
		
		return $action_bar;
	}
}
?>