<?php
/**
 * $Id: forum_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum
 */
/**
 * @author Michael Kyndt
 */

//require_once dirname(__FILE__) . '/forum_display_component.class.php';

class ForumDisplay extends ComplexDisplay
{
    const ACTION_VIEW_FORUM = 'view_forum';
    const ACTION_VIEW_TOPIC = 'view_topic';
    const ACTION_PUBLISH_FORUM = 'publish';
    
    const ACTION_CREATE_FORUM_POST = 'add_post';
    const ACTION_EDIT_FORUM_POST = 'edit_post';
    const ACTION_DELETE_FORUM_POST = 'delete_post';
    const ACTION_QUOTE_FORUM_POST = 'quote_post';
    
    const ACTION_CREATE_TOPIC = 'create_topic';
    const ACTION_DELETE_TOPIC = 'delete_topic';
    
    const ACTION_CREATE_SUBFORUM = 'create_subforum';
    const ACTION_EDIT_SUBFORUM = 'edit_subforum';
    const ACTION_DELETE_SUBFORUM = 'delete_subforum';
    const ACTION_MOVE_SUBFORUM = 'move_subforum';
    
    const ACTION_MAKE_IMPORTANT = 'make_important';
    const ACTION_MAKE_STICKY = 'make_sticky';
	
    function run()
    {
    	if (! $component)
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
	            case self :: ACTION_MOVE_SUBFORUM :
	                $component = $this->create_component('ForumSubforumMover');
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
	                $this->set_action(self :: ACTION_VIEW_CLO);
	                $component = $this->create_component('ForumViewer');
	        }
        }
    	$component->run();
    }
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
	
}

?>