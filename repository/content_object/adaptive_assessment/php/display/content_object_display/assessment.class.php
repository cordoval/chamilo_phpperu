<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Request;
use repository\ComplexDisplay;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentAssessmentContentObjectDisplay extends AdaptiveAssessmentContentObjectDisplay
{

    function display_content_object($content_object, $adaptive_assessment_item_attempt_data, $continue_url, $previous_url, $jump_urls)
    {
        $parameters = array();
        $parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = null;
        $parameters[self :: PARAM_EMBEDDED_CONTENT_OBJECT_ID] = $content_object->get_id();

        $html[] = $this->display_link($this->get_parent()->get_url($parameters));

        return implode("\n", $html);
    }
}
?>