<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete locations objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_LOCATION];
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

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLocationsSelected')));
		}
	}
}
?>