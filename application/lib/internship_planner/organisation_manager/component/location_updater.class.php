<?php

require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path().'lib/internship_planner/forms/location_form.class.php';


class InternshipOrganisationManagerLocationUpdaterComponent extends InternshipOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganisationManager :: PARAM_ACTION => InternshipOrganisationManager :: ACTION_BROWSE_LOCATIONS)), Translation :: get('BrowseInternshipLocations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateInternshipLocation')));

		$location = $this->retrieve_location(Request :: get(InternshipOrganisationManager :: PARAM_LOCATION_ID));
		$form = new InternshipLocationForm(InternshipLocationForm :: TYPE_EDIT, $location, $this->get_url(array(InternshipOrganisationManager :: PARAM_LOCATION_ID => $location->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location();
			$this->redirect($success ? Translation :: get('InternshipLocationUpdated') : Translation :: get('InternshipLocationNotUpdated'), !$success, array(InternshipOrganisationManager :: PARAM_ACTION => InternshipOrganisationManager :: ACTION_BROWSE_LOCATIONS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>