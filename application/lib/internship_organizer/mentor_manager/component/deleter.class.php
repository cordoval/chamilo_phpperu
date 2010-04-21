<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager_component.class.php';

class InternshipOrganizerMentorManagerDeleterComponent extends InternshipOrganizerMentorManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[InternshipOrganizerMentorManager :: PARAM_MENTOR_ID];
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
					$message = 'SelectedInternshipOrganizerMentorNotDeleted';
				}
				else
				{
					$message = 'Selected{InternshipOrganizerMentorsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedInternshipOrganizerMentorDeleted';
				}
				else
				{
					$message = 'SelectedInternshipOrganizerMentorsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(InternshipOrganizerMentorManager :: PARAM_ACTION => InternshipOrganizerMentorManager :: ACTION_BROWSE_MENTOR));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerMentorsSelected')));
		}
	}
}
?>