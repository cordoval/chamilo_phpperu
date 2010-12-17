<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Path;
use repository\ComplexBuilderComponent;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentBuilderDeleterComponent extends AdaptiveAssessmentBuilder
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>