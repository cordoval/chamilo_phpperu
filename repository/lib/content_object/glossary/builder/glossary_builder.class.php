<?php
/**
 * $Id: glossary_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary
 */
//require_once dirname(__FILE__) . '/glossary_builder_component.class.php';

class GlossaryBuilder extends ComplexBuilder
{

    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE : 
            	$component = $this->create_component('Browser');
                break;
            default:
            	$this->set_action(ComplexBuilder :: ACTION_BROWSE);
                $component = $this->create_component('Browser');
                break;
        }

        $component->run();
    }
    public function get_application_component_path() {
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}

?>