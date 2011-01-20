<?php
namespace repository\content_object\indicator;

use repository\ComplexBuilderComponent;

class IndicatorBuilderUpdaterComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}