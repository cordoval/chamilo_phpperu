<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */

class SurveyBuilderDeleterComponent extends SurveyBuilder
{
    function run()
    {
        $deleter = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: DELETER_COMPONENT, $this);
        $deleter->run();
    }
}

?>