<?php
namespace repository\content_object\forum;

use common\libraries\Translation;
use common\libraries\EqualityCondition;
use repository\RepositoryDataManager;
use repository\ComplexDisplay;
use repository\ComplexContentObjectItem;

/**
 * $Id: forum_post_deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum.component
 */

require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayForumPostDeleterComponent extends ForumDisplay
{

    function run()
    {
        if ($this->get_parent()->is_allowed(DELETE_RIGHT))
        {
            $posts = $this->get_selected_complex_content_object_item_id();

        	if (! is_array($posts))
            {
                $posts = array($posts);
            }

            $datamanager = RepositoryDataManager :: get_instance();
            $params = array();
            $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_TOPIC;
            $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

            foreach ($posts as $index => $post)
            {
                $complex_content_object_item = $datamanager->retrieve_complex_content_object_item($post);
                $complex_content_object_item->delete();

                $siblings = $datamanager->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $complex_content_object_item->get_parent()));
                if ($siblings == 0)
                {
                    $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
                    $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
                }
            }
            if (count($posts) > 1)
            {
                $message = htmlentities(Translation :: get('ObjectsDeleted', array('POST' => Translation :: get('ForumPosts')) , Utilities :: COMMON_LIBRARIES));
            }
            else
            {
                $message = htmlentities(Translation :: get('ObjectDeleted', array('POST' => Translation :: get('ForumPost')) , Utilities :: COMMON_LIBRARIES));
            }

            $this->redirect($message, false, $params);
        }
    }

}
?>