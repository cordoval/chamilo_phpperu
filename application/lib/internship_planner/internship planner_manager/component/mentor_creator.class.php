<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/mentor_form.class.php';

/**
 * Component to create a new mentor object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerMentorCreatorComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_MENTORS)), Translation :: get('BrowseMentors')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateMentor')));

		$mentor = new Mentor();
		$form = new MentorForm(MentorForm :: TYPE_CREATE, $mentor, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_mentor();
			$this->redirect($success ? Translation :: get('MentorCreated') : Translation :: get('MentorNotCreated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_MENTORS));
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