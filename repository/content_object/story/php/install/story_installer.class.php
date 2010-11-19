<?php
namespace repository\content_object\story;

use repository\ContentObjectInstaller;

/**
 * This class is used to install the story content object
 *
 * @package repository.lib.content_object.story
 * @author Hans De Bisschop
 */

class StoryContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>