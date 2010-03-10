<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_rel_category_form.class.php';

/**
 * Component to create a new location_rel_category object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerLocationRelCategoryCreatorComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_CATEGORIES)), Translation :: get('BrowseLocationRelCategories')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateLocationRelCategory')));

		$location_rel_category = new LocationRelCategory();
		$form = new LocationRelCategoryForm(LocationRelCategoryForm :: TYPE_CREATE, $location_rel_category, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_location_rel_category();
			$this->redirect($success ? Translation :: get('LocationRelCategoryCreated') : Translation :: get('LocationRelCategoryNotCreated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_CATEGORIES));
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