<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/location_form.class.php';

class InternshipOrganizerOrganisationManagerLocationCreatorComponent extends InternshipOrganizerOrganisationManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$organisation_id = $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
		$organisation = $this->retrieve_organisation($organisation_id);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganizerOrganisationManager::PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $organisation->get_name()));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_CREATE_LOCATION)), Translation :: get('CreateInternshipOrganizerLocation')));
			
		$location = new InternshipOrganizerLocation();
		$location->set_organisation_id($organisation_id);
				
		$form = new InternshipOrganizerLocationForm(InternshipOrganizerLocationForm :: TYPE_CREATE, $location, $this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location();
			$this->redirect($success ? Translation :: get('InternshipOrganizerLocationCreated') : Translation :: get('InternshipOrganizerLocationNotCreated'), !$success, array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id));
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