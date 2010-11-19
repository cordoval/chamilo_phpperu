<?php
namespace repository\content_object\survey_page;

use repository\ComplexBuilderComponent;


class SurveyPageBuilderViewerComponent extends SurveyPageBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>