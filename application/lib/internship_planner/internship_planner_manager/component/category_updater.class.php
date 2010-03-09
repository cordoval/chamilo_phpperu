<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/category_form.class.php';

/**
 * Component to edit an existing category object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerCategoryUpdaterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternshipPlanner')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('BrowseCategories')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateCategory')));

		$category = $this->retrieve_category(Request :: get(InternshipPlannerManager :: PARAM_CATEGORY));
		$form = new CategoryForm(CategoryForm :: TYPE_EDIT, $category, $this->get_url(array(InternshipPlannerManager :: PARAM_CATEGORY => $category->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_category();
			$this->redirect($success ? Translation :: get('CategoryUpdated') : Translation :: get('CategoryNotUpdated'), !$success, array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_CATEGORIES));
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