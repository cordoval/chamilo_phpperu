<?php
namespace repository\content_object\indicator;


class IndicatorBuilderViewerComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}