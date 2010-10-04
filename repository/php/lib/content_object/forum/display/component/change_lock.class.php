<?php
/**
 * $Id: sticky.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */

class ForumDisplayChangeLockComponent extends ForumDisplay
{

    function run()
    {
        $wrapper = $this->get_selected_complex_content_object_item();
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($wrapper->get_ref());
        if($object->invert_locked())
        {
        	$succes = true;
        	$message = Translation :: get('LockChanged');
        }
        else
        {
        	$message= Translation :: get('LockNotChanged');
        }
        
        $params = array();
        if($object->get_type() == Forum :: get_type_name())
        {
        	$params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
        }
        else
        {
        	$params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
        }
        $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        
        $this->redirect($message, !$succes, $params);
    }
}

?>