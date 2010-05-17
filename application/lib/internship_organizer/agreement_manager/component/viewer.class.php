<?php

require_once dirname ( __FILE__ ) . '/../agreement_manager.class.php';

//require_once dirname ( __FILE__ ) . '/rel_moment_browser/rel_moment_browser_table.class.php';
require_once Path :: get_application_path(). 'lib/internship_organizer/agreement_manager/component/moment_browser/browser_table.class.php';


class InternshipOrganizerAgreementManagerViewerComponent extends InternshipOrganizerAgreementManager
{

	private $action_bar;
	private $agreement;

	function run() 
	{
		$agreement_id = $_GET[InternshipOrganizerAgreementManager::PARAM_AGREEMENT_ID];
		$this->agreement = $this->retrieve_agreement($agreement_id);

		$trail = new BreadcrumbTrail ();
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganizerAgreementManager::PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT) ), Translation::get ( 'BrowseInternshipOrganizerAgreements' ) ) );
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganizerAgreementManager::PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id)), $this->agreement->get_name()) );

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
		$parameters[InternshipOrganizerAgreementManager::PARAM_AGREEMENT_ID] = $this->agreement->get_id();
		$table = new InternshipOrganizerMomentBrowserTable ( $this, $parameters , $this->get_condition () );
		return $table->as_html ();
	}


	function get_action_bar() 
	{
		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerMoment'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_moment_url($this->agreement), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


		$action_bar->set_search_url ( $this->get_url (array(InternshipOrganizerAgreementManager::PARAM_AGREEMENT_ID => $this->agreement->get_id())) );

		return $action_bar;
	}

	function get_condition() 
	{

		$query = $this->action_bar->get_query ();
		$conditions = array();
		$agreement_id = $this->agreement->get_id();
		$conditions[] = new EqualityCondition(InternshipOrganizerMoment::PROPERTY_AGREEMENT_ID, $agreement_id);

		if (isset ( $query ) && $query != '') 
		{
			$search_conditions = array ();
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerMoment::PROPERTY_NAME, '*' . $query . '*' );
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerMoment::PROPERTY_STREET, '*' . $query . '*' );
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerMoment::PROPERTY_STREET_NUMBER, '*' . $query . '*' );
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerMoment::PROPERTY_CITY, '*' . $query . '*' );

			$conditions[] = new OrCondition ( $search_conditions );
		}
		return new AndCondition($conditions);
	}
}
?>