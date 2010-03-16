<?php

require_once dirname ( __FILE__ ) . '/../organisation_manager.class.php';
require_once dirname ( __FILE__ ) . '/../organisation_manager_component.class.php';

require_once dirname ( __FILE__ ) . '/browser/browser_table.class.php';

class InternshipOrganisationManagerBrowserComponent extends InternshipOrganisationManagerComponent {
	private $action_bar;
	
	function run() {
		$trail = new BreadcrumbTrail ();
		$trail->add ( new Breadcrumb ( $this->get_url (), Translation::get ( 'BrowseOrganisations' ) ) );
		
		$this->action_bar = $this->get_action_bar ();
		
		$this->display_header ( $trail );
		
		echo $this->action_bar->as_html ();
		echo '<div id="action_bar_browser">';
		
		echo '<div>';
		echo $this->get_table ();
		echo '</div>';
		echo '</div>';
		$this->display_footer ();
	}
	
	function get_table() {
		$table = new InternshipOrganisationBrowserTable ( $this, array () , $this->get_condition () );
		return $table->as_html ();
	}

	
	function get_action_bar() {
		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );
		
		$action_bar->set_search_url ( $this->get_url () );
				
		return $action_bar;
	}
	
	function get_condition() {
				
		$query = $this->action_bar->get_query ();
		$condition = null;
		
		if (isset ( $query ) && $query != '') {
			$search_conditions = array ();
			$search_conditions [] = new LikeCondition ( InternshipOrganisation::PROPERTY_NAME, $query );
			$search_conditions [] = new LikeCondition ( InternshipOrganisation::PROPERTY_DESCRIPTION, $query );
			$condition = new OrCondition ( $search_conditions );
		}
		return $condition;
	}
}
?>