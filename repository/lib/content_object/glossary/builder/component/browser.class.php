<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */
//require_once dirname(__FILE__) . '/../glossary_builder_component.class.php';
require_once Path :: get_repository_path() . '/../../glossary.class.php';

class GlossaryBuilderBrowserComponent extends GlossaryBuilder
{

    function run()
    {
        $browser = ComplexBuilderComponent::factory(Glossary::get_type_name(),ComplexBuilderComponent::BROWSER_COMPONENT, $this);
        
        $browser->run();
        
    }
}

?>