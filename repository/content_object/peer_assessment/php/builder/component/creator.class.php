<?php
namespace repository\content_object\peer_assessment;


class PeerAssessmentBuilderCreatorComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>