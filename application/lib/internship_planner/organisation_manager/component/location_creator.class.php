<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/forms/location_form.class.php';

class InternshipOrganisationManagerLocationCreatorComponent extends InternshipOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		
		$organisation_id = $_GET[InternshipOrganisationManager :: PARAM_ORGANISATION_ID];
		$organisation = $this->retrieve_organisation($organisation_id);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganisationManager :: PARAM_ACTION => InternshipOrganisationManager :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganisations')));
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganisationManager::PARAM_ACTION => InternshipOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $organisation->get_name()));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganisationManager :: PARAM_ACTION => InternshipOrganisationManager :: ACTION_CREATE_LOCATION)), Translation :: get('CreateInternshipLocation')));
		
		
			
		$location = new InternshipLocation();
		$location->set_organisation_id($organisation_id);
				
		$form = new InternshipLocationForm(InternshipLocationForm :: TYPE_CREATE, $location, $this->get_url(array(InternshipOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location();
			$this->redirect($success ? Translation :: get('InternshipLocationCreated') : Translation :: get('InternshipLocationNotCreated'), !$success, array(InternshipOrganisationManager :: PARAM_ACTION => InternshipOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id));
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