<?php
/**
 * $Id: sticky.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */

class ForumToolChangeLockComponent extends ForumTool
{

    function run()
    {
        $forum_publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(ForumTool :: PARAM_PUBLICATION_ID));
        $object = $forum_publication->get_content_object();
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
        $params[ForumTool :: PARAM_ACTION] = ForumTool :: ACTION_BROWSE_FORUMS;
        
        $this->redirect($message, !$succes, $params);
    }
}

?>