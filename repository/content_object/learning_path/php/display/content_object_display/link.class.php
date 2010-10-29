<?php
namespace repository\content_object\learning_path;

/**
 * @package repository.content_object.learning_path
 */

class LearningPathLinkContentObjectDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($content_object, $learning_path_item_attempt_data, $continue_url, $previous_url, $jump_urls)
    {
        $html[] = $this->add_tracking_javascript();
        $html[] = $this->display_link($content_object->get_url());
        return implode("\n", $html);
    }
}
?>