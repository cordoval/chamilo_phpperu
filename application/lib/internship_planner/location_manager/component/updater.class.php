<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager.class.php';
require_once Path :: get_application_path().'lib/internship_planner/internship_planner_manager/internship_planner_manager_component.class.php';
require_once Path :: get_application_path().'lib/internship_planner/forms/location_form.class.php';

/**
 * Component to edit an existing location object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipLocationManagerUpdaterComponent extends InternshipLocationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipLocationManager :: PARAM_ACTION => InternshipLocationManager :: ACTION_BROWSE_LOCATIONS)), Translation :: get('BrowseInternshipLocations')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateInternshipLocation')));

		$location = $this->retrieve_location(Request :: get(InternshipLocationManager :: PARAM_LOCATION_ID));
		$form = new InternshipLocationForm(InternshipLocationForm :: TYPE_EDIT, $location, $this->get_url(array(InternshipLocationManager :: PARAM_LOCATION_ID => $location->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location();
			$this->redirect($success ? Translation :: get('InternshipLocationUpdated') : Translation :: get('InternshipLocationNotUpdated'), !$success, array(InternshipLocationManager :: PARAM_ACTION => InternshipLocationManager :: ACTION_BROWSE_LOCATIONS));
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