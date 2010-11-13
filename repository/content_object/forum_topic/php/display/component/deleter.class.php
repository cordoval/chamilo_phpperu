<?php
namespace repository\content_object\forum_topic;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use repository\RepositoryDataManager;
use repository\ComplexDisplay;
use repository\ComplexContentObjectItem;

class ForumTopicDisplayDeleterComponent extends ForumTopicDisplay
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
            $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
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
                $message = htmlentities(Translation :: get('ObjectsDeleted', array('OBJECT' => Translation :: get('ForumPosts', null ,'repository\content_object\forum')) , Utilities :: COMMON_LIBRARIES));
            }
            else
            {
                $message = htmlentities(Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('ForumPost', null ,'repository\content_object\forum')) , Utilities :: COMMON_LIBRARIES));
            }

            $this->redirect($message, false, $params);
        }
    }

}
?>