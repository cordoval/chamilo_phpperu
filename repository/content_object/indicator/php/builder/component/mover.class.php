<?php
namespace repository\content_object\indicator;


class IndicatorBuilderMoverComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}