<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */

class AssessmentBuilderCreatorComponent extends AssessmentBuilder
{
    function run()
    {
        $creator = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: CREATOR_COMPONENT, $this);
        $creator->run();
    }
}

?>