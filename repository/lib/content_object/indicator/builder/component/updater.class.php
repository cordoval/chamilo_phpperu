<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class IndicatorBuilderUpdaterComponent extends IndicatorBuilder
{

    function run()
    {
        $updater = ComplexBuilderComponent :: factory(ComplexBuilderComponent::UPDATER_COMPONENT, $this);
        $updater->run();
    }
}
?>