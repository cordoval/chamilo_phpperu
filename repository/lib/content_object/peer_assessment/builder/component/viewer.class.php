<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class PeerAssessmentBuilderViewerComponent extends PeerAssessmentBuilder
{

    function run()
    {
        $viewer = ComplexBuilderComponent :: factory(ComplexBuilderComponent::VIEWER_COMPONENT, $this);
        $viewer->run();
    }
}
?>