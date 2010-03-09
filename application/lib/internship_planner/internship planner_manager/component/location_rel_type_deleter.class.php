<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete location_rel_types objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationRelTypeDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_LOCATION_REL_TYPE];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$location_rel_type = $this->retrieve_location_rel_type($id);

				if (!$location_rel_type->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationRelTypeNotDeleted';
				}
				else
				{
					$message = 'Selected{LocationRelTypesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationRelTypeDeleted';
				}
				else
				{
					$message = 'SelectedLocationRelTypesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_TYPES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLocationRelTypesSelected')));
		}
	}
}
?>