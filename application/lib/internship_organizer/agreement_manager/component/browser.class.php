<?php

require_once dirname ( __FILE__ ) . '/../agreement_manager.class.php';
require_once dirname ( __FILE__ ) . '/browser/browser_table.class.php';

class InternshipOrganizerAgreementManagerBrowserComponent extends InternshipOrganizerAgreementManager 
{
	private $action_bar;

	function run() 
	{
		$trail = new BreadcrumbTrail ();
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganizerAgreementManager::PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT) ), Translation::get ( 'BrowseAgreements' ) ) );

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

	function get_table() 
	{
		$parameters = $this->get_parameters();
		$table = new InternshipOrganizerAgreementBrowserTable ( $this, $parameters , $this->get_condition () );
		return $table->as_html ();
	}


	function get_action_bar() 
	{
		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('AddAgreement'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_agreement_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


		$action_bar->set_search_url ( $this->get_url () );

		return $action_bar;
	}

	function get_condition()
	{

		$query = $this->action_bar->get_query ();
		$condition = null;

		if (isset ( $query ) && $query != '') 
		{
			$search_conditions = array ();
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerAgreement::PROPERTY_NAME, '*' . $query . '*' );
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerAgreement::PROPERTY_DESCRIPTION, '*' . $query . '*' );
			$condition = new OrCondition ( $search_conditions );
		}
		return $condition;
	}
}
?>