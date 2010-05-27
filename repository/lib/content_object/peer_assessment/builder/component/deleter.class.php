<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class PeerAssessmentBuilderDeleterComponent extends PeerAssessmentBuilder
{

    function run()
    {
        $deleter = ComplexBuilderComponent :: factory(ComplexBuilderComponent::DELETER_COMPONENT, $this);
        $deleter->run();
    }
}
?>