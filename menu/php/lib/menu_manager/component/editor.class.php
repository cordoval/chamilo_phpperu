<?php
namespace menu;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Request;

abstract class MenuManagerEditorComponent extends MenuManager
{
	function run()
	{
		$this->check_allowed();
		$item = $this->retrieve_navigation_item(Request :: get(MenuManager :: PARAM_ITEM));

        $form = $this->get_edit_form($item);

        if ($form->validate())
        {
            $success = $form->update_navigation_item();
            
            if($success)
            {
            	if($item->get_is_category() == 0)
            	{
            		$message = Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('MenuManagerItem')) , Utilities :: COMMON_LIBRARIES);
            	}
            	else
            	{
            		$message = Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('MenuManagerCategory')) , Utilities :: COMMON_LIBRARIES);
            	}
            }
            else
            {
            	if($item->get_is_category() == 0)
            	{
            		$message = Translation :: get('ObjectNotUpdated', array('OBJECT' => Translation :: get('MenuManagerItem')) , Utilities :: COMMON_LIBRARIES);
            	}
            	else
            	{
            		$message = Translation :: get('ObjectNotUpdated', array('OBJECT' => Translation :: get('MenuManagerCategory')) , Utilities :: COMMON_LIBRARIES);
            	}
            }
            
            $this->redirect($message, ($success ? false : true), array(
            		MenuManager :: PARAM_ACTION => MenuManager :: ACTION_BROWSE,
            		MenuManager :: PARAM_ITEM => $form->get_navigation_item()->get_category()));
        }
        else
        {
            $this->display_form($form);
        }
	}
	
	abstract function get_edit_form($item);
}

?>