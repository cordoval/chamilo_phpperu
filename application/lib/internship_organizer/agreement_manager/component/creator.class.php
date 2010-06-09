<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/agreement_form.class.php';

class InternshipOrganizerAgreementManagerCreatorComponent extends InternshipOrganizerAgreementManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateInternshipOrganizerAgreement')));

		$agreement = new InternshipOrganizerAgreement();
		$form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_CREATE, $agreement, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_agreement();
			$this->redirect($success ? Translation :: get('InternshipOrganizerAgreementCreated') : Translation :: get('InternshipOrganizerAgreementNotCreated'), !$success, array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT));
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