<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class PeerAssessmentBuilderParentChangerComponent extends PeerAssessmentBuilder
{

    function run()
    {
        $parent_changer = ComplexBuilderComponent :: factory(ComplexBuilderComponent::PARENT_CHANGER_COMPONENT, $this);
        $parent_changer->run();
    }
}
?>