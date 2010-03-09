<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/location_rel_category_form.class.php';

/**
 * Component to edit an existing location_rel_category object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationRelCategoryUpdaterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_CATEGORIES)), Translation :: get('BrowseLocationRelCategories')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLocationRelCategory')));

		$location_rel_category = $this->retrieve_location_rel_category(Request :: get(Internship plannerManager :: PARAM_LOCATION_REL_CATEGORY));
		$form = new LocationRelCategoryForm(LocationRelCategoryForm :: TYPE_EDIT, $location_rel_category, $this->get_url(array(Internship plannerManager :: PARAM_LOCATION_REL_CATEGORY => $location_rel_category->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_location_rel_category();
			$this->redirect($success ? Translation :: get('LocationRelCategoryUpdated') : Translation :: get('LocationRelCategoryNotUpdated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_CATEGORIES));
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