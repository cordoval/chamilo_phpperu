<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum_topic.component
 */
require_once dirname(__FILE__) . '/../../forum_topic.class.php';

class ForumTopicBuilderCreatorComponent extends ForumTopicBuilder
{
    function run()
    {
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: CREATOR_COMPONENT, $this);
        $browser->run();
    }
}

?>