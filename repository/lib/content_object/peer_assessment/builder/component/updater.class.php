<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class PeerAssessmentBuilderUpdaterComponent extends PeerAssessmentBuilder
{

    function run()
    {
        $updater = ComplexBuilderComponent :: factory(ComplexBuilderComponent::UPDATER_COMPONENT, $this);
        $updater->run();
    }
}
?>