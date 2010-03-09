<?php

class GradebookManagerGradebookTruncaterComponent extends GradebookManagerComponent
{
	function run()
	{ 
		$user = $this->get_user();
		
		if (!GradebookRights :: is_allowed(GradebookRights :: DELETE_RIGHT, GradebookRights :: LOCATION_BROWSER, 'gradebook_component'))
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
				if (!$gradebook->truncate())
				{
					$failures++;
				}
				else
				{
					//Events :: trigger_event('empty', 'group', array('target_group_id' => $group->get_id(), 'action_user_id' => $user->get_id()));
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGradebookNotEmptied';
				}
				else
				{
					$message = 'SelectedGradebooksNotEmptied';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGradebookEmptied';
				}
				else
				{
					$message = 'SelectedGradebooksEmptied';
				}
				
			}
			
			if(count($ids) == 1)
				$this->redirect( Translation :: get($message), ($failures ? true : false), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_GRADEBOOK, GradebookManager :: PARAM_GRADEBOOK_ID => $ids[0]));
			else
				$this->redirect( Translation :: get($message), ($failures ? true : false), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGradebookSelected')));
		}
	}
}
?>