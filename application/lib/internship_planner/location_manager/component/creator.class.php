<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path() . 'lib/internship_planner/forms/location_form.class.php';
/**
 * Component to create a new location object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipLocationManagerCreatorComponent extends InternshipLocationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshiplocationManager :: PARAM_ACTION => InternshiplocationManager :: ACTION_BROWSE_LOCATIONS)), Translation :: get('BrowseInternshipLocations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateInternshipLocation')));

		$location = new InternshipLocation();
		$form = new InternshipLocationForm(InternshipLocationForm :: TYPE_CREATE, $location, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location();
			$this->redirect($success ? Translation :: get('InternshipLocationCreated') : Translation :: get('InternshipLocationNotCreated'), !$success, array(InternshipLocationManager :: PARAM_ACTION => InternshipLocationManager :: ACTION_BROWSE_LOCATIONS));
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