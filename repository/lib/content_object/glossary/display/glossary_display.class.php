<?php
/**
 * $Id: glossary_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.glossary
 */

require_once dirname(__FILE__) . '/glossary_display_component.class.php';
/**
 * This tool allows a user to publish glossarys in his or her course.
 */
class GlossaryDisplay extends ComplexDisplay
{
    const ACTION_VIEW_GLOSSARY = 'view';

    /**
     * Inherited.
     */
    function run()
    {
        $component = parent :: run();
        
        if (! $component)
        {
            $action = $this->get_action();
            
            switch ($action)
            {
                case self :: ACTION_VIEW_GLOSSARY :
                    $component = $this->create_component('GlossaryViewer');
                    break;
                default :
                    $component = create_component('GlossaryViewer');
            }
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
		return dirname(__FILE__) . '/component/';
    }
}
?>