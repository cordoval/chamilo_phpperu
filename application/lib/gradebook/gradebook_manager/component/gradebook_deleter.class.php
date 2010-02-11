<?php

class GradebookManagerGradebookDeleterComponent extends GradebookManagerComponent
{
	function run()
	{
		$user = $this->get_user();

		$trail = new BreadcrumbTrail();

		if (!GradebookRights :: is_allowed(GradebookRights :: DELETE_RIGHT, 'browser', 'gradebook_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$ids = $_GET[GradebookManager :: PARAM_GRADEBOOK_ID];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
				
			foreach ($ids as $id)
			{
				$gradebook = $this->retrieve_gradebook($id);

				if (!$gradebook->delete())
				{
					$failures++;
				}
				else
				{
					//Events :: trigger_event('delete', 'group', array('target_group_id' => $group->get_id(), 'action_user_id' => $user->get_id()));
				}
			}
				
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGradebookNotDeleted';
				}
				else
				{
					$message = 'SelectedGradebooksNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGradebooksDeleted';
				}
				else
				{
					$message = 'SelectedGradebookDeleted';
				}
			}
				
			$this->redirect( Translation :: get($message), ($failures ? true : false), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGradebooksSelected')));
		}
	}
}
?>