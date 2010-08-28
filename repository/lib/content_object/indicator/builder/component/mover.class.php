<?php
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class IndicatorBuilderMoverComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>