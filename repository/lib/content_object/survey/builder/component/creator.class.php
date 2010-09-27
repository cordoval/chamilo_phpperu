<?php
class SurveyBuilderCreatorComponent extends SurveyBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>