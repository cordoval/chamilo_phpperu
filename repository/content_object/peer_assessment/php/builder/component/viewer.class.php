<?php
namespace repository\content_object\peer_assessment;


class PeerAssessmentBuilderViewerComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}