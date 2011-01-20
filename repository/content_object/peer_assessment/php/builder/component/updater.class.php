<?php
namespace repository\content_object\peer_assessment;

use repository\ComplexBuilderComponent;

class PeerAssessmentBuilderUpdaterComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>