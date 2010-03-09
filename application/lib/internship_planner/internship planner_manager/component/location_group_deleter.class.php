<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete location_groups objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationGroupDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_LOCATION_GROUP];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$location_group = $this->retrieve_location_group($id);

				if (!$location_group->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationGroupNotDeleted';
				}
				else
				{
					$message = 'Selected{LocationGroupsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationGroupDeleted';
				}
				else
				{
					$message = 'SelectedLocationGroupsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_GROUPS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLocationGroupsSelected')));
		}
	}
}
?>