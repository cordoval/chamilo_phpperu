<?php
namespace repository\content_object\peer_assessment;

class PeerAssessmentBuilderUpdaterComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>