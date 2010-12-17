<?php
namespace repository\content_object\adaptive_assessment;

use repository\ComplexContentObjectItem;
use repository\content_object\adaptive_assessment_item\AdaptiveAssessmentItem;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class ComplexAdaptiveAssessment extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(AdaptiveAssessment :: get_type_name(),
                AdaptiveAssessmentItem :: get_type_name());
    }
}
?>