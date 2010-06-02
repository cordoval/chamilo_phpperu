<?php
/**
 * $Id: mover.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */

class ForumManagerMoverComponent extends ForumManager
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $move = 0;
            $fpid = Request :: get(ForumManager :: PARAM_PUBLICATION_ID);
            if (Request :: get(ForumManager :: PARAM_MOVE))
            {
                $move = Request :: get(ForumManager :: PARAM_MOVE);
            }
            
            $datamanager = ForumDataManager :: get_instance();
            $publication = $datamanager->retrieve_forum_publication($fpid);
            if ($publication->move($move))
            {
                $message = Translation :: get('ContentObjectPublicationMoved');
            }
            else
            {
            	$message = Translation :: get('ContentObjectPublicationNotMoved');
            }
            
            $this->redirect($message, false, array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE));
        }
    }
}
?>