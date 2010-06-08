<?php
/**
 * $Id: content_object_updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
class WikiDisplayContentObjectUpdaterComponent extends WikiDisplay
{

    function run()
    {
        $browser = ComplexDisplayComponent :: factory(ComplexDisplayComponent :: CONTENT_OBJECT_UPDATER_COMPONENT, $this);
        $browser->run();
    }
}
?>