<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';

/**
 * Component to delete moments objects
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerMomentDeleterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipPlannerManager :: PARAM_MOMENT];
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

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_MOMENTS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoMomentsSelected')));
		}
	}
}
?>