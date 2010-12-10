<?php
namespace repository\content_object\adaptive_assessment;

use repository\ComplexBuilderComponent;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentBuilderViewerComponent extends AdaptiveAssessmentBuilder
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