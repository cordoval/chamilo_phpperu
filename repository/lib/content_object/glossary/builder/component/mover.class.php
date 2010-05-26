<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */
require_once dirname(__FILE__) . '/../../glossary.class.php';

class GlossaryBuilderMoverComponent extends GlossaryBuilder
{
    function run()
    {
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: MOVER_COMPONENT, $this);
        $browser->run();
    }
}

?>