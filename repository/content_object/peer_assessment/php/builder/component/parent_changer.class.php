<?php
namespace repository\content_object\peer_assessment;


class PeerAssessmentBuilderParentChangerComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}