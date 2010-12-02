<?php
namespace repository\content_object\survey;

use repository\ComplexBuilderComponent;



class SurveyBuilderViewerComponent extends SurveyBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
