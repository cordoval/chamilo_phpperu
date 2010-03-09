<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete moments objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerMomentDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_MOMENT];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$moment = $this->retrieve_moment($id);

				if (!$moment->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMomentNotDeleted';
				}
				else
				{
					$message = 'Selected{MomentsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMomentDeleted';
				}
				else
				{
					$message = 'SelectedMomentsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_MOMENTS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoMomentsSelected')));
		}
	}
}
?>