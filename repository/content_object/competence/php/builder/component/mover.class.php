<?php
namespace repository\content_object\competence;

use common\libraries\Path;

class CompetenceBuilderMoverComponent extends CompetenceBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>