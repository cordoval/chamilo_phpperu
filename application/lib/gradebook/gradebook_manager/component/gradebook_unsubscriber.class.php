<?php

class GradebookManagerGradebookUnsubscriberComponent extends GradebookManagerComponent
{
	
	function run()
	{
		$user = $this->get_user();

		if (!GradebookRights :: is_allowed(GradebookRights :: DELETE_RIGHT, 'browser', 'gradebook_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
				
		$ids = $_GET[GradebookManager :: PARAM_GRADEBOOK_REL_USER_ID];
			
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
				
			foreach ($ids as $id)
			{
				$gradebookreluser_ids = explode('|', $id);
				$gradebookreluser = $this->retrieve_gradebook_rel_user($gradebookreluser_ids[1], $gradebookreluser_ids[0]);

				if(!isset($gradebookreluser)) continue;

				if ($gradebookreluser_ids[0] == $gradebookreluser->get_gradebook_id())
				{
					if (!$gradebookreluser->delete())
					{
						$failures++;
					}
					else
					{
						//Events :: trigger_event('unsubscribe_user', 'group', array('target_group_id' => $groupreluser->get_group_id(), 'target_user_id' => $groupreluser->get_user_id(), 'action_user_id' => $user->get_id()));
					}
				}
				else
				{
					$failures++;
				}
			}
				
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGradebookRelUserNotDeleted';
				}
				else
				{
					$message = 'SelectedGradebookRelUsersNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGradebookRelUserDeleted';
				}
				else
				{
					$message = 'SelectedGradebookRelUsersDeleted';
				}
			}
				
			$this->redirect( Translation :: get($message), ($failures ? true : false), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_GRADEBOOK, GradebookManager :: PARAM_GRADEBOOK_ID => $gradebookreluser_ids[0]));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGradebookRelUserSelected')));
		}
	}
}
?>