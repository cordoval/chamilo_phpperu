<?php

class DescriptionToolRightsEditorComponent extends DescriptionTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: RIGHTS_EDITOR_COMPONENT, $this);
        $component->run();
    }
    
    function get_available_rights()
    {
    	return WeblcmsRights :: get_available_rights();
    }
}
?>