<?php
namespace repository\content_object\forum_topic;

use repository\ComplexDisplayPreview;
use repository\ComplexDisplay;

class ForumTopicComplexDisplayPreview extends ComplexDisplayPreview implements
        ForumTopicComplexDisplaySupport
{

    function run()
    {
        ComplexDisplay :: launch(ForumTopic :: get_type_name(), $this);
    }

    /**
     * Since this is a preview, no actual view event is triggered.
     *
     * @param $complex_topic_id
     */
    function forum_topic_viewed($complex_topic_id)
    {
    }

    /**
     * Since this is a preview, no views are logged and no count can
     * be retrieved.
     *
     * @param $complex_topic_id
     * @return string
     */
    function forum_count_topic_views($complex_topic_id)
    {
        return '-';
    }

    /**
     * Considering that this is a preview, always return true
     *
     * @param $right
     * @return boolean
     */
    function is_allowed($right)
    {
        return true;
    }
}
?>