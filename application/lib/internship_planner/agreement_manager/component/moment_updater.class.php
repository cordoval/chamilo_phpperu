<?php

require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path().'lib/internship_planner/forms/moment_form.class.php';


class InternshipPlannerAgreementManagerMomentUpdaterComponent extends InternshipPlannerAgreementManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		
		$moment = $this->retrieve_moment(Request :: get(InternshipPlannerAgreementManager :: PARAM_MOMENT_ID));
		
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_view_agreement_url($moment->get_agreement()), Translation :: get('ViewAgreement')));
		$trail->add(new Breadcrumb($this->get_update_moment_url($moment), Translation :: get('UpdateMoment')));

		$form = new InternshipPlannerMomentForm(InternshipPlannerMomentForm :: TYPE_EDIT, $moment, $this->get_url(array(InternshipPlannerAgreementManager :: PARAM_MOMENT_ID => $moment->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_moment();
			$this->redirect($success ? Translation :: get('InternshipPlannerMomentUpdated') : Translation :: get('InternshipPlannerMomentNotUpdated'), !$success, array(InternshipPlannerAgreementManager :: PARAM_ACTION => InternshipPlannerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipPlannerAgreementManager :: PARAM_AGREEMENT_ID => $moment->get_agreement_id()));
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