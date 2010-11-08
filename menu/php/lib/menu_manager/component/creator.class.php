<?php
namespace menu;
use common\libraries\Translation;
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
            		$message = Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('MenuManagerItem')) , Utilities :: COMMON_LIBRARIES);
            	}
            	else
            	{
            		$message = Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('MenuManagerCategory')) , Utilities :: COMMON_LIBRARIES);
            	}
            }
            else
            {
            	if($item->get_is_category() == 0)
            	{
            		$message = Translation :: get('ObjectNotCreated', array('OBJECT' => Translation :: get('MenuManagerItem')) , Utilities :: COMMON_LIBRARIES);
            	}
            	else
            	{
            		$message = Translation :: get('ObjectNotCreated', array('OBJECT' => Translation :: get('MenuManagerCategory')) , Utilities :: COMMON_LIBRARIES);
            	}
            }
            
            $this->redirect($message, ($success ? false : true), array(
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