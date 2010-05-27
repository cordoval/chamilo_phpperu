<?php
/**
 * $Id: glossary_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.glossary
 */

/**
 * This tool allows a user to publish glossarys in his or her course.
 */
class GlossaryDisplay extends ComplexDisplay
{
    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
            
        switch ($action)
        {
            case self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT :
                $component = $this->create_component('GlossaryViewer');
                break;
            default :
             	$this->set_action(self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT);
                $component = $this->create_component('GlossaryViewer');
        }
        $component->run();
    }

    function get_application_component_path()
    {
		return dirname(__FILE__) . '/component/';
    }
}
?>