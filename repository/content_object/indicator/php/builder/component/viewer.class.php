<?php
namespace repository\content_object\indicator;

use repository\ComplexBuilderComponent;

class IndicatorBuilderViewerComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}