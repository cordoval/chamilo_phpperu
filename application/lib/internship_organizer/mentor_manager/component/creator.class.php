<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/mentor_form.class.php';

class InternshipOrganizerMentorManagerCreatorComponent extends InternshipOrganizerMentorManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerMentorManager :: PARAM_ACTION => InternshipOrganizerMentorManager :: ACTION_BROWSE_MENTOR)), Translation :: get('BrowseInternshipOrganizerMentors')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateInternshipOrganizerMentor')));

		$mentor = new InternshipOrganizerMentor();
		$form = new InternshipOrganizerMentorForm(InternshipOrganizerMentorForm :: TYPE_CREATE, $mentor, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_mentor();
			$this->redirect($success ? Translation :: get('InternshipOrganizerMentorCreated') : Translation :: get('InternshipOrganizerMentorNotCreated'), !$success, array(InternshipOrganizerMentorManager :: PARAM_ACTION => InternshipOrganizerMentorManager :: ACTION_BROWSE_MENTOR));
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