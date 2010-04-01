<?php

require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/forms/moment_form.class.php';

class InternshipPlannerAgreementManagerMomentCreatorComponent extends InternshipPlannerAgreementManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		
		$agreement_id = $_GET[InternshipPlannerAgreementManager :: PARAM_AGREEMENT_ID];
		$agreement = $this->retrieve_agreement($agreement_id);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerAgreementManager :: PARAM_ACTION => InternshipPlannerAgreementManager :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipPlannerAgreements')));
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipPlannerAgreementManager::PARAM_ACTION => InternshipPlannerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipPlannerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id)), $agreement->get_name()));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerAgreementManager :: PARAM_ACTION => InternshipPlannerAgreementManager :: ACTION_CREATE_MOMENT)), Translation :: get('CreateInternshipPlannerMoment')));
		
		
			
		$moment = new InternshipPlannerMoment();
		$moment->set_agreement_id($agreement_id);
				
		$form = new InternshipPlannerMomentForm(InternshipPlannerMomentForm :: TYPE_CREATE, $moment, $this->get_url(array(InternshipPlannerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id)), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_moment();
			$this->redirect($success ? Translation :: get('InternshipPlannerMomentCreated') : Translation :: get('InternshipPlannerMomentNotCreated'), !$success, array(InternshipPlannerAgreementManager :: PARAM_ACTION => InternshipPlannerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipPlannerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id));
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