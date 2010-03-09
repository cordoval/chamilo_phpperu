<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete periods objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerPeriodDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_PERIOD];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$period = $this->retrieve_period($id);

				if (!$period->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPeriodNotDeleted';
				}
				else
				{
					$message = 'Selected{PeriodsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPeriodDeleted';
				}
				else
				{
					$message = 'SelectedPeriodsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_PERIODS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPeriodsSelected')));
		}
	}
}
?>