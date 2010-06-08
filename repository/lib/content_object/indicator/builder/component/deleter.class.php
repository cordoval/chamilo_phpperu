<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class IndicatorBuilderDeleterComponent extends IndicatorBuilder
{

    function run()
    {
        $deleter = ComplexBuilderComponent :: factory(ComplexBuilderComponent::DELETER_COMPONENT, $this);
        $deleter->run();
    }
}
?>