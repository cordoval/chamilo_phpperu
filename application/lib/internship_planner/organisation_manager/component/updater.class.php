<?php

require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path().'lib/internship_planner/forms/organisation_form.class.php';


class InternshipOrganisationManagerUpdaterComponent extends InternshipOrganisationManagerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganisationManager :: PARAM_ACTION => InternshipOrganisationManager :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganisations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateInternshipOrganisation')));

		$organisation = $this->retrieve_organisation(Request :: get(InternshipOrganisationManager :: PARAM_ORGANISATION_ID));
		$form = new InternshipOrganisationForm(InternshipOrganisationForm :: TYPE_EDIT, $organisation, $this->get_url(array(InternshipOrganisationManager :: PARAM_ORGANISATION_ID => $organisation->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location();
			$this->redirect($success ? Translation :: get('InternshipOrganisationUpdated') : Translation :: get('InternshipOrganisationNotUpdated'), !$success, array(InternshipOrganisationManager :: PARAM_ACTION => InternshipOrganisationManager :: ACTION_BROWSE_ORGANISATION));
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