<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */

class AssessmentBuilderMoverComponent extends AssessmentBuilder
{
    function run()
    {
        $mover = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: MOVER_COMPONENT, $this);
        $mover->run();
    }
}

?>