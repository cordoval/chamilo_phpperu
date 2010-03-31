<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/forms/location_form.class.php';

class InternshipPlannerOrganisationManagerLocationCreatorComponent extends InternshipPlannerOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		
		$organisation_id = $_GET[InternshipPlannerOrganisationManager :: PARAM_ORGANISATION_ID];
		$organisation = $this->retrieve_organisation($organisation_id);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerOrganisationManager :: PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipPlannerOrganisations')));
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipPlannerOrganisationManager::PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipPlannerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $organisation->get_name()));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerOrganisationManager :: PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_CREATE_LOCATION)), Translation :: get('CreateInternshipPlannerLocation')));
		
		
			
		$location = new InternshipPlannerLocation();
		$location->set_organisation_id($organisation_id);
				
		$form = new InternshipPlannerLocationForm(InternshipPlannerLocationForm :: TYPE_CREATE, $location, $this->get_url(array(InternshipPlannerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location();
			$this->redirect($success ? Translation :: get('InternshipPlannerLocationCreated') : Translation :: get('InternshipPlannerLocationNotCreated'), !$success, array(InternshipPlannerOrganisationManager :: PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipPlannerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id));
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