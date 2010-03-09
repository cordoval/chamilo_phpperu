<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_rel_mentor_form.class.php';

/**
 * Component to create a new location_rel_mentor object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationRelMentorCreatorComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_MENTORS)), Translation :: get('BrowseLocationRelMentors')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateLocationRelMentor')));

		$location_rel_mentor = new LocationRelMentor();
		$form = new LocationRelMentorForm(LocationRelMentorForm :: TYPE_CREATE, $location_rel_mentor, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location_rel_mentor();
			$this->redirect($success ? Translation :: get('LocationRelMentorCreated') : Translation :: get('LocationRelMentorNotCreated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_MENTORS));
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