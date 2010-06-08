<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class IndicatorBuilderViewerComponent extends IndicatorBuilder
{

    function run()
    {
        $viewer = ComplexBuilderComponent :: factory(ComplexBuilderComponent::VIEWER_COMPONENT, $this);
        $viewer->run();
    }
}
?>