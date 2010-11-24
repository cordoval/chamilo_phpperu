<?php
namespace repository\content_object\forum;

use repository\ComplexDisplaySupport;

/**
 * A class implements the <code>ForumComplexDisplaySupport</code> interface to
 * indicate that it will serve as a launch base for a ForumComplexDisplay.
 *
 * @author  Hans De Bisschop
 */

interface ForumComplexDisplaySupport extends ComplexDisplaySupport
{

    /**
     * Trigger the event (and corresponding trackers) that
     * mark a forum topic as "viewed"
     *
     * @param int $complex_topic_id
     */
    function forum_topic_viewed($complex_topic_id);

    /**
     * Count the number of views for the given topic
     *
     * @param unknown_type $complex_topic_id
     */
    function forum_count_topic_views($complex_topic_id);
}
?>