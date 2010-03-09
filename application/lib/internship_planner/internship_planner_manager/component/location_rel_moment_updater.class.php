<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_rel_moment_form.class.php';

/**
 * Component to edit an existing location_rel_moment object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerLocationRelMomentUpdaterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_MOMENTS)), Translation :: get('BrowseLocationRelMoments')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLocationRelMoment')));

		$location_rel_moment = $this->retrieve_location_rel_moment(Request :: get(InternshipPlannerManager :: PARAM_LOCATION_REL_MOMENT));
		$form = new LocationRelMomentForm(LocationRelMomentForm :: TYPE_EDIT, $location_rel_moment, $this->get_url(array(InternshipPlannerManager :: PARAM_LOCATION_REL_MOMENT => $location_rel_moment->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location_rel_moment();
			$this->redirect($success ? Translation :: get('LocationRelMomentUpdated') : Translation :: get('LocationRelMomentNotUpdated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_MOMENTS));
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