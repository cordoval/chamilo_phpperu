<?php
namespace repository\content_object\indicator;

class IndicatorBuilderUpdaterComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}