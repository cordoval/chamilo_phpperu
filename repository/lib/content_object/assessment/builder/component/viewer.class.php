<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */

class AssessmentBuilderViewerComponent extends AssessmentBuilder
{
    function run()
    {
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: VIEWER_COMPONENT, $this);
        $browser->run();
    }
}

?>