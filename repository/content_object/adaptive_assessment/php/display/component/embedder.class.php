<?php
namespace repository\content_object\adaptive_assessment;

require_once dirname(__FILE__) . '/../adaptive_assessment_display_embedder.class.php';

class AdaptiveAssessmentDisplayEmbedderComponent extends AdaptiveAssessmentDisplay
{

    function run()
    {
        AdaptiveAssessmentDisplayEmbedder :: launch($this);
    }
}
?>