<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_form.class.php';

/**
 * Component to edit an existing location object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationUpdaterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATIONS)), Translation :: get('BrowseLocations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLocation')));

		$location = $this->retrieve_location(Request :: get(Internship plannerManager :: PARAM_LOCATION));
		$form = new LocationForm(LocationForm :: TYPE_EDIT, $location, $this->get_url(array(Internship plannerManager :: PARAM_LOCATION => $location->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location();
			$this->redirect($success ? Translation :: get('LocationUpdated') : Translation :: get('LocationNotUpdated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATIONS));
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