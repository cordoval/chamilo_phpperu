<?php
/**
 * $Id: forum_topic_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum_topic
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class ForumTopicBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('ForumTopic', $component_name, $builder);
    }
}

?>