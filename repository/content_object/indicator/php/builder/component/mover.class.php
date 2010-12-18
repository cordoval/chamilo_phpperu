<?php
namespace repository\content_object\indicator;

use repository\ComplexBuilderComponent;

class IndicatorBuilderMoverComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}