<?php
namespace repository\content_object\wiki;

use repository\ComplexDisplayComponent;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */
require_once dirname(__FILE__) . '/../../wiki.class.php';

class WikiDisplayDeleterComponent extends WikiDisplay
{
    function run()
    {
        ComplexDisplayComponent :: launch($this);
    }
}

?>