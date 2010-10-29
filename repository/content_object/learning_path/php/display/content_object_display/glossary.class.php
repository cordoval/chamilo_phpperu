<?php
namespace repository\content_object\learning_path;

use common\libraries\Request;
use repository\ComplexDisplay;

require_once dirname(__FILE__) . '/../learning_path_display_embedder.class.php';

/**
 * @package repository.content_object.learning_path
 */

class LearningPathGlossaryContentObjectDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($glossary)
    {
        $parameters = array();
        $parameters[self :: PARAM_EMBEDDED_CONTENT_OBJECT_ID] = $glossary->get_id();

        $html[] = $this->add_tracking_javascript();
        $html[] = $this->display_link($this->get_parent()->get_url($parameters));

        return implode("\n", $html);
    }
}
?>