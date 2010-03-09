<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete location_rel_categories objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationRelCategoryDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_LOCATION_REL_CATEGORY];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$location_rel_category = $this->retrieve_location_rel_category($id);

				if (!$location_rel_category->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationRelCategoryNotDeleted';
				}
				else
				{
					$message = 'Selected{LocationRelCategoriesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationRelCategoryDeleted';
				}
				else
				{
					$message = 'SelectedLocationRelCategoriesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_CATEGORIES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLocationRelCategoriesSelected')));
		}
	}
}
?>