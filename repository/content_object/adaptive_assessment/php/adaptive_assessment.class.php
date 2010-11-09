<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Utilities;
use common\libraries\ComplexContentObjectSupport;

use repository\ContentObject;
use repository\content_object\adaptive_assessment_item\AdaptiveAssessmentItem;

/**
 * @author Hans De Bisschop
 */
class AdaptiveAssessment extends ContentObject implements
        ComplexContentObjectSupport
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function get_allowed_types()
    {
        return array(
                AdaptiveAssessment :: get_type_name(),
                AdaptiveAssessmentItem :: get_type_name());
    }
}
?>