<?php
namespace repository\content_object\indicator;
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class IndicatorBuilderViewerComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>