<?php
namespace repository\content_object\glossary;
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */
require_once dirname(__FILE__) . '/../../glossary.class.php';

class GlossaryBuilderUpdaterComponent extends GlossaryBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>