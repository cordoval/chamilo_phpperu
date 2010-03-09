<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete categories objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerCategoryDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_CATEGORY];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$category = $this->retrieve_category($id);

				if (!$category->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCategoryNotDeleted';
				}
				else
				{
					$message = 'Selected{CategoriesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCategoryDeleted';
				}
				else
				{
					$message = 'SelectedCategoriesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_CATEGORIES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoCategoriesSelected')));
		}
	}
}
?>