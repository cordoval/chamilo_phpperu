<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/category_form.class.php';

/**
 * Component to create a new category object
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerCategoryCreatorComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE)), Translation :: get('BrowseInternship planner')));
		$trail->add(new Breadcrumb($this->get_url(array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('BrowseCategories')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateCategory')));

		$category = new Category();
		$form = new CategoryForm(CategoryForm :: TYPE_CREATE, $category, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_category();
			$this->redirect($success ? Translation :: get('CategoryCreated') : Translation :: get('CategoryNotCreated'), !$success, array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_CATEGORIES));
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