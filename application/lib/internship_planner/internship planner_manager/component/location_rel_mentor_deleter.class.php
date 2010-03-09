<?php
/**
 * @package application.internship planner.internship planner.component
 */
require_once dirname(__FILE__).'/../internship planner_manager.class.php';
require_once dirname(__FILE__).'/../internship planner_manager_component.class.php';

/**
 * Component to delete location_rel_mentors objects
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerManagerLocationRelMentorDeleterComponent extends Internship plannerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[Internship plannerManager :: PARAM_LOCATION_REL_MENTOR];
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

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Internship plannerManager :: PARAM_ACTION => Internship plannerManager :: ACTION_BROWSE_LOCATION_REL_MENTORS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLocationRelMentorsSelected')));
		}
	}
}
?>