<?php
namespace repository\content_object\peer_assessment;

class PeerAssessmentBuilderDeleterComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}