<?php
namespace repository\content_object\peer_assessment;


use repository\ComplexBuilderComponent;

class PeerAssessmentBuilderCreatorComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>