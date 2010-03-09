<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_form.class.php';

/**
 * Component to create a new location object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationCreatorComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATIONS)), Translation :: get('BrowseLocations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateLocation')));

		$location = new Location();
		$form = new LocationForm(LocationForm :: TYPE_CREATE, $location, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location();
			$this->redirect($success ? Translation :: get('LocationCreated') : Translation :: get('LocationNotCreated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATIONS));
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