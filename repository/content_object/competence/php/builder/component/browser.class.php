<?php
namespace repository\content_object\competence;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.indicator.component
 */
//require_once dirname(__FILE__) . '/../indicator_builder_component.class.php';

class CompetenceBuilderBrowserComponent extends CompetenceBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>