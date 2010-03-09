<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_rel_moment_form.class.php';

/**
 * Component to edit an existing location_rel_moment object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationRelMomentUpdaterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_MOMENTS)), Translation :: get('BrowseLocationRelMoments')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLocationRelMoment')));

		$location_rel_moment = $this->retrieve_location_rel_moment(Request :: get(Internship plannerManager :: PARAM_LOCATION_REL_MOMENT));
		$form = new LocationRelMomentForm(LocationRelMomentForm :: TYPE_EDIT, $location_rel_moment, $this->get_url(array(Internship plannerManager :: PARAM_LOCATION_REL_MOMENT => $location_rel_moment->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location_rel_moment();
			$this->redirect($success ? Translation :: get('LocationRelMomentUpdated') : Translation :: get('LocationRelMomentNotUpdated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_MOMENTS));
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