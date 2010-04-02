<?php
require_once dirname ( __FILE__ ) . '/../category_manager.class.php';
require_once dirname ( __FILE__ ) . '/../category_manager_component.class.php';
require_once dirname ( __FILE__ ) . '/browser/browser_table.class.php';

class InternshipOrganizerCategoryManagerBrowserComponent extends InternshipOrganizerCategoryManagerComponent {
	private $ab;
	private $category;
	private $root_category;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run() {
		
		$trail = new BreadcrumbTrail ();
		
		$trail->add ( new Breadcrumb ( $this->get_url (), Translation::get ( 'BrowseCategories' ) ) );
		$trail->add_help ( 'category general' );
		
		$this->ab = $this->get_action_bar ();
		
		$menu = $this->get_menu_html ();
		$output = $this->get_browser_html ();
		
		$this->display_header ( $trail );
		echo $this->ab->as_html () . '<br />';
		echo $menu;
		echo $output;
		$this->display_footer ();
	}
	
	function get_browser_html() {
		$table = new InternshipOrganizerCategoryBrowserTable ( $this, $this->get_parameters (), $this->get_condition () );
		
		$html = array ();
		$html [] = '<div style="float: right; width: 80%;">';
		$html [] = $table->as_html ();
		$html [] = '</div>';
		$html [] = '<div class="clear"></div>';
		
		return implode ( $html, "\n" );
	}
	
	function get_menu_html() {
		$category_menu = new InternshipOrganizerCategoryMenu ( $this->get_category () );
		$html = array ();
		$html [] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
		$html [] = $category_menu->render_as_tree ();
		$html [] = '</div>';
		
		return implode ( $html, "\n" );
	}
	
	function get_category() {
		if (! $this->category) {
			$this->category = Request::get ( InternshipOrganizerCategoryManager::PARAM_CATEGORY_ID );
			
			if (! $this->category) {
				$this->category = $this->get_root_category ()->get_id ();
			}
		
		}
		
		return $this->category;
	}
	
	function get_root_category() {
		if (! $this->root_category) {
			$this->root_category = $this->retrieve_root_category ();
		}
		
		return $this->root_category;
	}
	
	function get_condition() {
		$condition = new EqualityCondition ( InternshipOrganizerCategory::PROPERTY_PARENT_ID, $this->get_category () );
		
		$query = $this->ab->get_query ();
		if (isset ( $query ) && $query != '') {
			$or_conditions = array ();
			$or_conditions [] = new PatternMatchCondition ( InternshipOrganizerCategory::PROPERTY_NAME, '*' . $query . '*' );
			$or_conditions [] = new PatternMatchCondition ( InternshipOrganizerCategory::PROPERTY_DESCRIPTION, '*' . $query . '*' );
			$or_condition = new OrCondition ( $or_conditions );
			
			$and_conditions = array ();
			$and_conditions [] = $condition;
			$and_conditions [] = $or_condition;
			$condition = new AndCondition ( $and_conditions );
		}
		
		return $condition;
	}
	
	function get_action_bar() {
		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );
		
		$action_bar->set_search_url ( $this->get_url ( array (InternshipOrganizerCategoryManager::PARAM_CATEGORY_ID => $this->get_category () ) ) );
		
		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'Add' ), Theme::get_common_image_path () . 'action_add.png', $this->get_create_category_url ( $this->get_category () ), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ViewRoot' ), Theme::get_common_image_path () . 'action_home.png', $this->get_browse_categories_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
		$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'ShowAll' ), Theme::get_common_image_path () . 'action_browser.png', $this->get_browse_categories_url (), ToolbarItem::DISPLAY_ICON_AND_LABEL ) );
		
		return $action_bar;
	}
}
?>