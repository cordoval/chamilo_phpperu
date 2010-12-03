<?php
namespace repository\content_object\indicator;

class IndicatorBuilderParentChangerComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>