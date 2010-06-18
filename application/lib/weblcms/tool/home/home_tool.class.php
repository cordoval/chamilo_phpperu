<?php
class HomeTool extends Tool
{
    const PARAM_TOOL = 'tool';
    const PARAM_VISIBILITY = 'visibility';
    const ACTION_CHANGE_TOOL_VISIBILITY = 'change_tool_visibility';
    
    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case self :: ACTION_VIEW :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_TOGGLE_VISIBILITY:
            	$component = $this->create_component('ToggleVisibility');
                break;
            case self :: ACTION_DELETE:
            	$component = $this->create_component('Deleter');
             	break;
            case self :: ACTION_CHANGE_TOOL_VISIBILITY:
            	$component = $this->create_component('ToolVisibilityChanger');
             	break;
            default :
                $component = $this->create_component('Viewer');
        }
        $component->run();
    }
	
    function set_optional_parameters()
    {
		
    }
    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }
}
?>