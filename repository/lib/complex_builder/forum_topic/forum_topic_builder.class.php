<?php
/**
 * $Id: forum_topic_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum_topic
 */
require_once dirname(__FILE__) . '/forum_topic_builder_component.class.php';

class ForumTopicBuilder extends ComplexBuilder
{

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE_CLO :
                $component = ForumTopicBuilderComponent :: factory('Browser', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }
}

?>