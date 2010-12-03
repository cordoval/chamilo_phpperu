<?php
namespace repository\content_object\indicator;

use common\libraries\Path;

class IndicatorBuilderCreatorComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}