<?php
namespace repository\content_object\assessment;

use repository\ComplexBuilderComponent;
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */

class AssessmentBuilderMoverComponent extends AssessmentBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>