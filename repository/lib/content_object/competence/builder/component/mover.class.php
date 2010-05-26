<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class CompetenceBuilderMoverComponent extends CompetenceBuilder
{

    function run()
    {
        $mover = ComplexBuilderComponent :: factory(ComplexBuilderComponent::MOVER_COMPONENT, $this);
        $mover->run();
    }
}
?>