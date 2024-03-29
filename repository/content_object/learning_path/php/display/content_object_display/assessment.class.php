<?php
namespace repository\content_object\learning_path;

use common\libraries\Request;
use repository\ComplexDisplay;

require_once dirname(__FILE__) . '/../learning_path_display_embedder.class.php';

/**
 * @package repository.content_object.learning_path
 */

class LearningPathAssessmentContentObjectDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($content_object, $learning_path_item_attempt_data, $continue_url, $previous_url, $jump_urls)
    {
        $parameters = array();
        $parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = null;
        $parameters[self :: PARAM_EMBEDDED_CONTENT_OBJECT_ID] = $content_object->get_id();
        
        $html[] = $this->display_link($this->get_parent()->get_url($parameters));
        
        return implode("\n", $html);
    }
}
?>