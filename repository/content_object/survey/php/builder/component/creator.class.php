<?php namespace repository\content_object\survey;
namespace repository\content_object\survey;
class SurveyBuilderCreatorComponent extends SurveyBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>