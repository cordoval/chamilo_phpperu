<?php
/**
 * $Id: forum_topic_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum_topic
 */

class ForumTopicBuilder extends ComplexBuilder
{

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECT :
                $component = ForumTopicBuilderComponent :: factory('Browser', $this);
                break;
            case ComplexBuilder :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = ForumTopicBuilderComponent :: factory('Deleter', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }
}

?>