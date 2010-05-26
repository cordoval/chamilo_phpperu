<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class IndicatorBuilderCreatorComponent extends IndicatorBuilder
{

    function run()
    {
        $creator = ComplexBuilderComponent :: factory(ComplexBuilderComponent::CREATOR_COMPONENT, $this);
        $creator->run();
    }
}
?>