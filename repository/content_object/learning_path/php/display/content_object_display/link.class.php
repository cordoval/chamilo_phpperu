<?php
namespace repository\content_object\learning_path;

/**
 * @package repository.content_object.learning_path
 */

class LearningPathLinkContentObjectDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($link)
    {
        $html[] = $this->add_tracking_javascript();
        $html[] = $this->display_link($link->get_url());
        return implode("\n", $html);
    }
}
?>