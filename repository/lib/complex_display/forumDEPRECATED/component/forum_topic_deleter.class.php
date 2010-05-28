<?php
/**
 * $Id: forum_topic_deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum.component
 */
require_once dirname(__FILE__) . '/../forum_display.class.php';
require_once dirname(__FILE__) . '/../forum_display_component.class.php';

class ForumDisplayForumTopicDeleterComponent extends ForumDisplayComponent
{

    function run()
    {
        if ($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $forum = Request :: get('forum');
            $topics = Request :: get('topic');
            $is_subforum = Request :: get('is_subforum');
            $pid = Request :: get('pid');
            
            $posts = Request :: get('post');
            
            if (! is_array($topics))
            {
                $topics = array($topics);
            }
            
            $datamanager = RepositoryDataManager :: get_instance();
            $params = array('pid' => $pid);
            $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
            
            if ($is_subforum)
                $params['forum'] = $forum;
            
            foreach ($topics as $topic)
            {
                $cloi = $datamanager->retrieve_complex_content_object_item($topic);
                $cloi->delete();
            }
            if (count($topics) > 1)
            {
                $message = htmlentities(Translation :: get('ForumTopicsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ForumTopicDeleted'));
            }
            
            $this->redirect($message, false, $params);
        }
    }
}
?>