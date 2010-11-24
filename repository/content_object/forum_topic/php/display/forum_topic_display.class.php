<?php
namespace repository\content_object\forum_topic;

use common\libraries\Request;
use repository\ComplexDisplay;
use repository\RepositoryDataManager;
use repository\content_object\forum_topic\ComplexForumTopic;

/**
 * @author Hans De Bisschop
 */

class ForumTopicDisplay extends ComplexDisplay
{
    const ACTION_CREATE_FORUM_POST = 'creator';
    const ACTION_EDIT_FORUM_POST = 'editor';
    const ACTION_DELETE_FORUM_POST = 'deleter';
    const ACTION_QUOTE_FORUM_POST = 'quoter';

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function forum_topic_viewed($complex_topic_id)
    {
        return $this->get_parent()->forum_topic_viewed($complex_topic_id);
    }

    function forum_count_topic_views($complex_topic_id)
    {
        return $this->get_parent()->forum_count_topic_views($complex_topic_id);
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_DISPLAY_ACTION;
    }
}
?>