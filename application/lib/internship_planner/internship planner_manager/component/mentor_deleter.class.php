<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete mentors objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerMentorDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_MENTOR];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$mentor = $this->retrieve_mentor($id);

				if (!$mentor->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMentorNotDeleted';
				}
				else
				{
					$message = 'Selected{MentorsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedMentorDeleted';
				}
				else
				{
					$message = 'SelectedMentorsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_MENTORS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoMentorsSelected')));
		}
	}
}
?>