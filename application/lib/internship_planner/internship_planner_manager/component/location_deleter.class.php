<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';

/**
 * Component to delete locations objects
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerLocationDeleterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipPlannerManager :: PARAM_LOCATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$location = $this->retrieve_location($id);

				if (!$location->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationNotDeleted';
				}
				else
				{
					$message = 'Selected{LocationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationDeleted';
				}
				else
				{
					$message = 'SelectedLocationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLocationsSelected')));
		}
	}
}
?>