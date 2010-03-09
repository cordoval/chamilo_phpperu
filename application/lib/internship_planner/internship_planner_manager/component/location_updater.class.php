<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_form.class.php';

/**
 * Component to edit an existing location object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerLocationUpdaterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATIONS)), Translation :: get('BrowseLocations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLocation')));

		$location = $this->retrieve_location(Request :: get(InternshipPlannerManager :: PARAM_LOCATION));
		$form = new LocationForm(LocationForm :: TYPE_EDIT, $location, $this->get_url(array(InternshipPlannerManager :: PARAM_LOCATION => $location->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location();
			$this->redirect($success ? Translation :: get('LocationUpdated') : Translation :: get('LocationNotUpdated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATIONS));
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