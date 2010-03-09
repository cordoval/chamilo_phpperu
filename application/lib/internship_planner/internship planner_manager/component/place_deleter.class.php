<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete places objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerPlaceDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_PLACE];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$place = $this->retrieve_place($id);

				if (!$place->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPlaceNotDeleted';
				}
				else
				{
					$message = 'Selected{PlacesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPlaceDeleted';
				}
				else
				{
					$message = 'SelectedPlacesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PLACES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPlacesSelected')));
		}
	}
}
?>