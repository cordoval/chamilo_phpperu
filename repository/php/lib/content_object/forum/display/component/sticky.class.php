<?php
/**
 * $Id: sticky.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */
require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayStickyComponent extends ForumDisplay
{

    function run()
    {
        $topic = $this->get_selected_complex_content_object_item();
        
        if ($topic->get_type() == 1)
        {
            $topic->set_type(null);
            $message = 'TopicUnStickied';
        }
        else
        {
            $topic->set_type(1);
            $message = 'TopicStickied';
        }
        $topic->update();
        
        $params = array();
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
        $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        
        $this->redirect(Translation :: get($message), '', $params);
    }
}

?>