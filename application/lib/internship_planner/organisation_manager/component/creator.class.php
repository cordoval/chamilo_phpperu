<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/forms/organisation_form.class.php';

class InternshipPlannerOrganisationManagerCreatorComponent extends InternshipPlannerOrganisationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerOrganisationManager :: PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipPlannerOrganisations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateInternshipPlannerOrganisation')));

		$organisation = new InternshipPlannerOrganisation();
		$form = new InternshipPlannerOrganisationForm(InternshipPlannerOrganisationForm :: TYPE_CREATE, $organisation, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_organisation();
			$this->redirect($success ? Translation :: get('InternshipPlannerOrganisationCreated') : Translation :: get('InternshipPlannerOrganisationNotCreated'), !$success, array(InternshipPlannerOrganisationManager :: PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_BROWSE_ORGANISATION));
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