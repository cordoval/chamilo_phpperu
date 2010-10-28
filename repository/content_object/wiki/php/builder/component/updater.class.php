<?php
namespace repository\content_object\wiki;

use repository\ComplexBuilderComponent;
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */

class WikiBuilderUpdaterComponent extends WikiBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>