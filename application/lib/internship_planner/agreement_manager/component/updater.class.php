<?php

require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path().'lib/internship_planner/forms/agreement_form.class.php';


class InternshipPlannerAgreementManagerUpdaterComponent extends InternshipPlannerAgreementManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerAgreementManager :: PARAM_ACTION => InternshipPlannerAgreementManager :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipPlannerAgreements')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateInternshipPlannerAgreement')));

		$agreement = $this->retrieve_agreement(Request :: get(InternshipPlannerAgreementManager :: PARAM_AGREEMENT_ID));
		$form = new InternshipPlannerAgreementForm(InternshipPlannerAgreementForm :: TYPE_EDIT, $agreement, $this->get_url(array(InternshipPlannerAgreementManager :: PARAM_AGREEMENT_ID => $agreement->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_agreement();
			$this->redirect($success ? Translation :: get('InternshipPlannerAgreementUpdated') : Translation :: get('InternshipPlannerAgreementNotUpdated'), !$success, array(InternshipPlannerAgreementManager :: PARAM_ACTION => InternshipPlannerAgreementManager :: ACTION_BROWSE_AGREEMENT));
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