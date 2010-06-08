<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/moment_form.class.php';

class InternshipOrganizerAgreementManagerMomentCreatorComponent extends InternshipOrganizerAgreementManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		
		$agreement_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID];
		$agreement = $this->retrieve_agreement($agreement_id);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
		$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganizerAgreementManager::PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id)), $agreement->get_name()));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_CREATE_MOMENT)), Translation :: get('CreateInternshipOrganizerMoment')));
		
		
			
		$moment = new InternshipOrganizerMoment();
		$moment->set_agreement_id($agreement_id);
				
		$form = new InternshipOrganizerMomentForm(InternshipOrganizerMomentForm :: TYPE_CREATE, $moment, $this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id)), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_moment();
			$this->redirect($success ? Translation :: get('InternshipOrganizerMomentCreated') : Translation :: get('InternshipOrganizerMomentNotCreated'), !$success, array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id));
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