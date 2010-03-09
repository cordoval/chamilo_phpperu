<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_rel_moment_form.class.php';

/**
 * Component to create a new location_rel_moment object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationRelMomentCreatorComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_MOMENTS)), Translation :: get('BrowseLocationRelMoments')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateLocationRelMoment')));

		$location_rel_moment = new LocationRelMoment();
		$form = new LocationRelMomentForm(LocationRelMomentForm :: TYPE_CREATE, $location_rel_moment, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location_rel_moment();
			$this->redirect($success ? Translation :: get('LocationRelMomentCreated') : Translation :: get('LocationRelMomentNotCreated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_MOMENTS));
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