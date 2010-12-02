<?php
namespace repository\content_object\indicator;

use common\libraries\Path;


class IndicatorBuilderDeleterComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}