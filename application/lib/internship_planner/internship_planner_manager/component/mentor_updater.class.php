<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/mentor_form.class.php';

/**
 * Component to edit an existing mentor object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerMentorUpdaterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_MENTORS)), Translation :: get('BrowseMentors')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateMentor')));

		$mentor = $this->retrieve_mentor(Request :: get(InternshipPlannerManager :: PARAM_MENTOR));
		$form = new MentorForm(MentorForm :: TYPE_EDIT, $mentor, $this->get_url(array(InternshipPlannerManager :: PARAM_MENTOR => $mentor->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_mentor();
			$this->redirect($success ? Translation :: get('MentorUpdated') : Translation :: get('MentorNotUpdated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_MENTORS));
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