<?php
namespace repository\content_object\survey;

use repository\ComplexBuilderComponent;
use common\libraries\Path;


class SurveyBuilderViewerComponent extends SurveyBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>