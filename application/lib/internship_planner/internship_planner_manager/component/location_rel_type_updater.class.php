<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_rel_type_form.class.php';

/**
 * Component to edit an existing location_rel_type object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerLocationRelTypeUpdaterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_TYPES)), Translation :: get('BrowseLocationRelTypes')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLocationRelType')));

		$location_rel_type = $this->retrieve_location_rel_type(Request :: get(InternshipPlannerManager :: PARAM_LOCATION_REL_TYPE));
		$form = new LocationRelTypeForm(LocationRelTypeForm :: TYPE_EDIT, $location_rel_type, $this->get_url(array(InternshipPlannerManager :: PARAM_LOCATION_REL_TYPE => $location_rel_type->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location_rel_type();
			$this->redirect($success ? Translation :: get('LocationRelTypeUpdated') : Translation :: get('LocationRelTypeNotUpdated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_TYPES));
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