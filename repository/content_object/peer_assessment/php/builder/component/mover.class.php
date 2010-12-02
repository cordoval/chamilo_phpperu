<?php
namespace repository\content_object\peer_assessment;

class PeerAssessmentBuilderMoverComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}