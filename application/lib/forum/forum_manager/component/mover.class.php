<?php
/**
 * $Id: mover.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */

class ForumManagerMoverComponent extends ForumManagerComponent
{

    function run()
    {
        if ($this->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $move = 0;
            $fpid = Request :: get(ForumManager :: PARAM_FORUM_PUBLICATION);
            if (Request :: get(ForumManager :: PARAM_MOVE))
            {
                $move = Request :: get(ForumManager :: PARAM_MOVE);
            }
            
            $datamanager = ForumDataManager :: get_instance();
            $publication = $datamanager->retrieve_forum_publication($fpid);
            if ($publication->move($move))
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationMoved'));
            }
            $this->redirect($message, false, array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE));
        }
    }
}
?>