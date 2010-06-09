<?php

require_once Path :: get_application_path().'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path().'lib/internship_organizer/forms/moment_form.class.php';


class InternshipOrganizerAgreementManagerMomentUpdaterComponent extends InternshipOrganizerAgreementManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		
		$moment = $this->retrieve_moment(Request :: get(InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID));
		
		
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_view_agreement_url($moment->get_agreement()), Translation :: get('ViewInternshipOrganizerAgreement')));
		$trail->add(new Breadcrumb($this->get_update_moment_url($moment), Translation :: get('UpdateInternshipOrganizerMoment')));

		$form = new InternshipOrganizerMomentForm(InternshipOrganizerMomentForm :: TYPE_EDIT, $moment, $this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_MOMENT_ID => $moment->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_moment();
			$this->redirect($success ? Translation :: get('InternshipOrganizerMomentUpdated') : Translation :: get('InternshipOrganizerMomentNotUpdated'), !$success, array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $moment->get_agreement_id()));
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