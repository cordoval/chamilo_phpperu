<?php
namespace repository\content_object\survey_page;
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */

class SurveyPageBuilderMoverComponent extends SurveyPageBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>