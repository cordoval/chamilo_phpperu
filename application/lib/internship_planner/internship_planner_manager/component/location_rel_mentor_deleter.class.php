<?php
/**
 * @package application.internship_planner.internship_planner.component
 */
require_once dirname(__FILE__).'/../internship_planner_manager.class.php';
require_once dirname(__FILE__).'/../internship_planner_manager_component.class.php';

/**
 * Component to delete location_rel_mentors objects
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerManagerLocationRelMentorDeleterComponent extends InternshipPlannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipPlannerManager :: PARAM_LOCATION_REL_MENTOR];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$location_rel_mentor = $this->retrieve_location_rel_mentor($id);

				if (!$location_rel_mentor->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationRelMentorNotDeleted';
				}
				else
				{
					$message = 'Selected{LocationRelMentorsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationRelMentorDeleted';
				}
				else
				{
					$message = 'SelectedLocationRelMentorsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_BROWSE_LOCATION_REL_MENTORS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLocationRelMentorsSelected')));
		}
	}
}
?>