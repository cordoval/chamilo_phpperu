<?php
namespace repository\content_object\forum_topic;

use common\libraries\Path;
use repository\ComplexBuilderComponent;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum_topic.component
 */

class ForumTopicBuilderBrowserComponent extends ForumTopicBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>