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
        if ($this->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $subforums = $this->get_selected_complex_content_object_item_id();
            
            if (!is_array($subforums))
            {
                $subforums = array($subforums);
            }
            
            $datamanager = RepositoryDataManager :: get_instance();
            $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
            $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
            
            foreach ($subforums as $subforum)
            {
                $complex_content_object_item = $datamanager->retrieve_complex_content_object_item($subforum);
                $complex_content_object_item->delete();
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