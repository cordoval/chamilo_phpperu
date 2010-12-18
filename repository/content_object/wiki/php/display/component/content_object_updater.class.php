<?php
namespace repository\content_object\wiki;
/**
 * $Id: content_object_updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
use repository\ComplexDisplayComponent;

class WikiDisplayContentObjectUpdaterComponent extends WikiDisplay
{

    function run()
    {
        ComplexDisplayComponent :: launch($this);
    }
}
?>