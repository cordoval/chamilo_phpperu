<?php 
namespace repository\content_object\survey;

use repository\ComplexBuilderComponent;

class SurveyBuilderCreatorComponent extends SurveyBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>