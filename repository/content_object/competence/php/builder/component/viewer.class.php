<?php
namespace repository\content_object\competence;

use common\libraries\Path;


class CompetenceBuilderViewerComponent extends CompetenceBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>