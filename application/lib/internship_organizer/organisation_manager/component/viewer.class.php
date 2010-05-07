<?php

require_once dirname ( __FILE__ ) . '/../organisation_manager.class.php';
//require_once dirname ( __FILE__ ) . '/../organisation_manager_component.class.php';

//require_once dirname ( __FILE__ ) . '/rel_location_browser/rel_location_browser_table.class.php';
require_once Path :: get_application_path(). 'lib/internship_organizer/organisation_manager/component/location_browser/browser_table.class.php';


class InternshipOrganizerOrganisationManagerViewerComponent extends InternshipOrganizerOrganisationManager 
{

	private $action_bar;
	private $organisation;

	function run() 
	{

		$organisation_id = $_GET[InternshipOrganizerOrganisationManager::PARAM_ORGANISATION_ID];
		$this->organisation = $this->retrieve_organisation($organisation_id);

		$trail = new BreadcrumbTrail ();
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganizerOrganisationManager::PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_ORGANISATION) ), Translation::get ( 'BrowseInternshipOrganizerOrganisations' ) ) );
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganizerOrganisationManager::PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $this->organisation->get_name()) );

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
		$parameters[InternshipOrganizerOrganisationManager::PARAM_ORGANISATION_ID] = $this->organisation->get_id();
		$table = new InternshipOrganizerLocationBrowserTable ( $this, $parameters , $this->get_condition () );
		return $table->as_html ();
	}


	function get_action_bar() 
	{
		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateInternshipOrganizerLocation'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_location_url($this->organisation), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


		$action_bar->set_search_url ( $this->get_url (array(InternshipOrganizerOrganisationManager::PARAM_ORGANISATION_ID => $this->organisation->get_id())) );

		return $action_bar;
	}

	function get_condition() 
	{

		$query = $this->action_bar->get_query ();
		$conditions = array();
		$organisation_id = $this->organisation->get_id();
		$conditions[] = new EqualityCondition(InternshipOrganizerLocation::PROPERTY_ORGANISATION_ID, $organisation_id);

		if (isset ( $query ) && $query != '') 
		{
			$search_conditions = array ();
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_NAME, '*' . $query . '*' );
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_STREET, '*' . $query . '*' );
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_STREET_NUMBER, '*' . $query . '*' );
			$search_conditions [] = new PatternMatchCondition ( InternshipOrganizerLocation::PROPERTY_CITY, '*' . $query . '*' );

			$conditions[] = new OrCondition ( $search_conditions );
		}
		return new AndCondition($conditions);
	}
}
?>