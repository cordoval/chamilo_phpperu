<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_rel_mentor_form.class.php';

/**
 * Component to edit an existing location_rel_mentor object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerLocationRelMentorUpdaterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_MENTORS)), Translation :: get('BrowseLocationRelMentors')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLocationRelMentor')));

		$location_rel_mentor = $this->retrieve_location_rel_mentor(Request :: get(InternshipPlannerManager :: PARAM_LOCATION_REL_MENTOR));
		$form = new LocationRelMentorForm(LocationRelMentorForm :: TYPE_EDIT, $location_rel_mentor, $this->get_url(array(InternshipPlannerManager :: PARAM_LOCATION_REL_MENTOR => $location_rel_mentor->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location_rel_mentor();
			$this->redirect($success ? Translation :: get('LocationRelMentorUpdated') : Translation :: get('LocationRelMentorNotUpdated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_MENTORS));
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