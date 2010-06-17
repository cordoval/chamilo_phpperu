<?php
class HomeTool extends Tool
{
    const PARAM_TOOL = 'target_tool';
    
    const ACTION_CHANGE_TOOL_VISIBILITY = 'change_tool_visibility';
    const ACTION_MAKE_TOOL_VISIBLE = 'tool_visible';
    const ACTION_MAKE_TOOL_INVISIBLE = 'tool_invisible';
    const ACTION_DELETE_LINKS = 'links_deleter';
    
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
            case self :: ACTION_SHOW_PUBLICATION:
            	$component = $this->create_component('ToggleVisibility');
            	Request :: set_get(HomeTool :: PARAM_VISIBILITY, 1);
                break;
            case self :: ACTION_HIDE_PUBLICATION:
            	$component = $this->create_component('ToggleVisibility');
            	Request :: set_get(HomeTool :: PARAM_VISIBILITY, 0);
                break;
            case self :: ACTION_DELETE:
            	$component = $this->create_component('Deleter');
             	break;
            case self :: ACTION_CHANGE_TOOL_VISIBILITY:
            	$component = $this->create_component('ToolVisibilityChanger');
             	break;
            case self :: ACTION_MAKE_TOOL_VISIBLE:
            	$component = $this->create_component('ToolVisibilityChanger');
            	Request :: set_get(HomeTool :: PARAM_VISIBILITY, 1);
             	break;
            case self :: ACTION_MAKE_TOOL_INVISIBLE:
            	$component = $this->create_component('ToolVisibilityChanger');
            	Request :: set_get(HomeTool :: PARAM_VISIBILITY, 0);
             	break;
            case self :: ACTION_PUBLISH_INTRODUCTION:
            	$component = $this->create_component('IntroductionPublisher');
             	break;
            case self :: ACTION_UPDATE:
            	$component = $this->create_component('Updater');
            	break;
            case self :: ACTION_DELETE_LINKS:
            	$component = $this->create_component('LinksDeleter');
            	break;
            default :
                $component = $this->create_component('Viewer');
        }
        $component->run();
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }
}
?>