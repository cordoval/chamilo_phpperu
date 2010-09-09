<?php
/**
 * $Id: sticky.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */

class ForumManagerChangeLockComponent extends ForumManager
{

    function run()
    {
        $forum_publication = $this->retrieve_forum_publication(Request :: get(ForumManager :: PARAM_PUBLICATION_ID));
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($forum_publication->get_forum_id());
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
        $params[ForumManager :: PARAM_ACTION] = ForumManager :: ACTION_BROWSE;
        
        $this->redirect($message, !$succes, $params);
    }
}

?>