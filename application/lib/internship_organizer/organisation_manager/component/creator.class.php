<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/organisation_form.class.php';

class InternshipOrganizerOrganisationManagerCreatorComponent extends InternshipOrganizerOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateInternshipOrganizerOrganisation')));

		$organisation = new InternshipOrganizerOrganisation();
		$form = new InternshipOrganizerOrganisationForm(InternshipOrganizerOrganisationForm :: TYPE_CREATE, $organisation, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_organisation();
			$this->redirect($success ? Translation :: get('InternshipOrganizerOrganisationCreated') : Translation :: get('InternshipOrganizerOrganisationNotCreated'), !$success, array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_ORGANISATION));
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