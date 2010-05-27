<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.indicator.component
 */
//require_once dirname(__FILE__) . '/../indicator_builder_component.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class PeerAssessmentBuilderBrowserComponent extends PeerAssessmentBuilder
{

    function run()
    {
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent::BROWSER_COMPONENT, $this);
        $browser->run();
        
    }
}

?>