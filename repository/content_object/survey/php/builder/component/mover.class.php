<?php
namespace repository\content_object\survey;
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */

class SurveyBuilderMoverComponent extends SurveyBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>