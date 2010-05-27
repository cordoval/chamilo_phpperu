<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */
require_once dirname(__FILE__) . '/../../wiki.class.php';

class WikiDisplayUpdaterComponent extends WikiDisplay
{
    function run()
    {
        $browser = ComplexDisplayComponent :: factory(ComplexDisplayComponent :: UPDATER_COMPONENT, $this);
        $browser->run();
    }
}

?>