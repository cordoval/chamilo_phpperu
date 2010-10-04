<?php
abstract class MenuManagerCreatorComponent extends MenuManager
{
	function run()
	{
		$this->check_allowed();
		$item = new NavigationItem();
        $form = $this->get_creation_form($item);

        if ($form->validate())
        {
            $success = $form->create_navigation_item();
            
            if($success)
            {
            	if($item->get_is_category() == 0)
            	{
            		$message = 'MenuManagerItemCreated';
            	}
            	else
            	{
            		$message = 'MenuManagerCategoryCreated';
            	}
            }
            else
            {
            	if($item->get_is_category() == 0)
            	{
            		$message = 'MenuManagerItemNotCreated';
            	}
            	else
            	{
            		$message = 'MenuManagerCategoryNotCreated';
            	}
            }
            
            $this->redirect(Translation :: get($message), ($success ? false : true), array(
            	MenuManager :: PARAM_ACTION => MenuManager :: ACTION_BROWSE,
            	MenuManager :: PARAM_ITEM => $item->get_category()));
        }
        else
        {
            $this->display_form($form);
        }
	}
	
	abstract function get_creation_form($item);
}

?>