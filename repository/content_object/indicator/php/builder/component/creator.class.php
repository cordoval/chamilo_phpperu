<?php
namespace repository\content_object\indicator;

use common\libraries\Path;

require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class IndicatorBuilderCreatorComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>