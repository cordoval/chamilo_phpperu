<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum_topic.component
 */
require_once Path :: get_repository_path() . '/lib/content_object/forum_topic/forum_topic.class.php';

class ForumTopicBuilderBrowserComponent extends ForumTopicBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>