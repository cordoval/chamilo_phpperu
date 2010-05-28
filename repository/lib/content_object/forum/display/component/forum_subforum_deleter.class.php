<?php
/**
 * $Id: forum_subforum_deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum.component
 */
require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayForumSubforumDeleterComponent extends ForumDisplay
{

    function run()
    {
        if ($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $forum = Request :: get('forum');
            $subforums = Request :: get('subforum');
            $is_subforum = Request :: get('is_subforum');
            $pid = Request :: get('pid');
            
            if (! is_array($subforums))
            {
                $subforums = array($subforums);
            }
            
            $datamanager = RepositoryDataManager :: get_instance();
            $params = array('pid' => $pid);
            $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
            
            if ($is_subforum)
                $params['forum'] = $forum;
            
            foreach ($subforums as $subforum)
            {
                $cloi = $datamanager->retrieve_complex_content_object_item($subforum);
                $cloi->delete();
            }
            if (count($subforums) > 1)
            {
                $message = htmlentities(Translation :: get('SubforumsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('SubforumDeleted'));
            }
            
            $this->redirect($message, false, $params);
        }
    }

}
?>