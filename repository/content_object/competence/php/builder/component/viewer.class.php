<?php
namespace repository\content_object\competence;

use repository\ComplexBuilderComponent;

use common\libraries\Path;

class CompetenceBuilderViewerComponent extends CompetenceBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>