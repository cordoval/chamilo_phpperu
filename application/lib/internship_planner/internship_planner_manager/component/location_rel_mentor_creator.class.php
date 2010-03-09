<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_rel_mentor_form.class.php';

/**
 * Component to create a new location_rel_mentor object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerLocationRelMentorCreatorComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_MENTORS)), Translation :: get('BrowseLocationRelMentors')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateLocationRelMentor')));

		$location_rel_mentor = new LocationRelMentor();
		$form = new LocationRelMentorForm(LocationRelMentorForm :: TYPE_CREATE, $location_rel_mentor, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location_rel_mentor();
			$this->redirect($success ? Translation :: get('LocationRelMentorCreated') : Translation :: get('LocationRelMentorNotCreated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_MENTORS));
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