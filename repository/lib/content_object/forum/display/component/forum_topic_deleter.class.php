<?php
/**
 * $Id: forum_topic_deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum.component
 */
require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayForumTopicDeleterComponent extends ForumDisplay
{

    function run()
    {
        if ($this->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $topics = $this->get_selected_complex_content_object_item_id();
            
            if (! is_array($topics))
            {
                $topics = array($topics);
            }
            
            $datamanager = RepositoryDataManager :: get_instance();

            $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
            $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
            
            foreach ($topics as $topic)
            {
                $complex_content_object_item = $datamanager->retrieve_complex_content_object_item($topic);
                $complex_content_object_item->delete();
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