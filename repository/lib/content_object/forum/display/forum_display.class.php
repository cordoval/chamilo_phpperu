<?php
/**
 * $Id: forum_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum
 */
/**
 * @author Michael Kyndt
 */

class ForumDisplay extends ComplexDisplay
{
    
    const ACTION_VIEW_FORUM = 'forum_viewer';
    const ACTION_VIEW_TOPIC = 'topic_viewer';
    const ACTION_PUBLISH_FORUM = 'publisher';
    
    const ACTION_CREATE_FORUM_POST = 'forum_post_creator';
    const ACTION_EDIT_FORUM_POST = 'forum_post_editor';
    const ACTION_DELETE_FORUM_POST = 'forum_post_deleter';
    const ACTION_QUOTE_FORUM_POST = 'forum_post_quoter';
    
    const ACTION_CREATE_TOPIC = 'forum_topic_creator';
    const ACTION_DELETE_TOPIC = 'forum_topic_deleter';
    
    const ACTION_CREATE_SUBFORUM = 'forum_subforum_creator';
    const ACTION_EDIT_SUBFORUM = 'forum_subforum_editor';
    const ACTION_DELETE_SUBFORUM = 'forum_subforum_deleter';
    
    const ACTION_MAKE_IMPORTANT = 'important';
    const ACTION_MAKE_STICKY = 'sticky';

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_PUBLISH_FORUM :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_VIEW_FORUM :
                $component = $this->create_component('ForumViewer');
                break;
            case self :: ACTION_VIEW_TOPIC :
                $component = $this->create_component('TopicViewer');
                break;
            case self :: ACTION_CREATE_FORUM_POST :
                $component = $this->create_component('ForumPostCreator');
                break;
            case self :: ACTION_EDIT_FORUM_POST :
                $component = $this->create_component('ForumPostEditor');
                break;
            case self :: ACTION_DELETE_FORUM_POST :
                $component = $this->create_component('ForumPostDeleter');
                break;
            case self :: ACTION_QUOTE_FORUM_POST :
                $component = $this->create_component('ForumPostQuoter');
                break;
            case self :: ACTION_CREATE_TOPIC :
                $component = $this->create_component('ForumTopicCreator');
                break;
            case self :: ACTION_DELETE_TOPIC :
                $component = $this->create_component('ForumTopicDeleter');
                break;
            case self :: ACTION_CREATE_SUBFORUM :
                $component = $this->create_component('ForumSubforumCreator');
                break;
            case self :: ACTION_EDIT_SUBFORUM :
                $component = $this->create_component('ForumSubforumEditor');
                break;
            case self :: ACTION_DELETE_SUBFORUM :
                $component = $this->create_component('ForumSubforumDeleter');
                break;
            case self :: ACTION_MAKE_IMPORTANT :
                $component = $this->create_component('Important');
                break;
            case self :: ACTION_MAKE_STICKY :
                $component = $this->create_component('Sticky');
                break;
            default :
                $this->set_action(self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT);
                if ($this->get_complex_content_object_item() instanceof ComplexForumTopic)
                    $component = $this->create_component('TopicViewer');
                else
                    $component = $this->create_component('ForumViewer');
        }
        $component->run();
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function topic_viewed($complex_topic_id)
    {
        return $this->get_parent()->topic_viewed($complex_topic_id);
    }

    function count_topic_views($complex_topic_id)
    {
        return $this->get_parent()->count_topic_views($complex_topic_id);
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
        $complex_content_object_item_id = Request :: get(self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        if ($complex_content_object_item_id)
        {
            $complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_content_object_item_id);
            if ($complex_content_object_item instanceof ComplexForumTopic)
            {
                return self :: ACTION_VIEW_TOPIC;
            }
            else
            {
                return self :: ACTION_VIEW_FORUM;
            }
        }
        else
        {
            return self :: DEFAULT_ACTION;
        }
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