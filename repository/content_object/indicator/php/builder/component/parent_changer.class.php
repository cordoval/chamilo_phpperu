<?php
namespace repository\content_object\indicator;

use repository\ComplexBuilderComponent;

class IndicatorBuilderParentChangerComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>