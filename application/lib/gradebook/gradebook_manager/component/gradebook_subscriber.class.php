<?php

class GradebookManagerGradebookSubscriberComponent extends GradebookManagerComponent
{
	function run()
	{ 
		$user = $this->get_user();

		if (!GradebookRights :: is_allowed(GradebookRights :: ADD_RIGHT, 'browser', 'gradebook_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}	
		
		$gradebook_id = $_GET[GradebookManager :: PARAM_GRADEBOOK_ID];
		$users = $_GET[GradebookManager :: PARAM_USER_ID];
						
		$failures = 0;
		
		if (!empty ($users))
		{
			if (!is_array($users))
			{
				$users = array ($users);
			}
			
			foreach($users as $user)
			{ 
				$existing_gradebookreluser = $this->retrieve_gradebook_rel_user($user, $gradebook_id);
							
				if (!$existing_gradebookreluser)
				{ 
					$gradebookreluser = new GradebookRelUser();
					$gradebookreluser->set_gradebook_id($gradebook_id);
					$gradebookreluser->set_user_id($user);
										
					if (!$gradebookreluser->create())
					{
						$failures++;
					}
					else
					{ 
						//Events :: trigger_event('subscribe_user', 'group', array('target_group_id' => $groupreluser->get_group_id(), 'target_user_id' => $groupreluser->get_user_id(), 'action_user_id' => $this->get_user()->get_id()));
					}
				}
				else
				{
					$contains_dupes = true;
				}
			}
			
			if ($failures)
			{
				if (count($users) == 1)
				{
					$message = 'SelectedUserNotAddedToGradebook' . ($contains_dupes ? 'Dupes' : '');
				}
				else
				{
					$message = 'SelectedUsersNotAddedToGradebook' . ($contains_dupes ? 'Dupes' : '');
				}
			}
			else
			{
				if (count($users) == 1)
				{
					$message = 'SelectedUserAddedToGradebook' . ($contains_dupes ? 'Dupes' : '');
				}
				else
				{
						$message = 'SelectedUsersAddedToGradebook' . ($contains_dupes ? 'Dupes' : '');
				}
			}
		
			
			$this->redirect( Translation :: get($message), ($failures ? true : false), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_GRADEBOOK, GradebookManager :: PARAM_GRADEBOOK_ID => $gradebook_id));
			exit;
			break;
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGradebookRelUserSelected')));
		}
	}
}
?>