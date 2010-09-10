<?php

class RightsToolRightsEditorComponent extends RightsTool
{

    function run()
    {
        ToolComponent::factory(ToolComponent::RIGHTS_EDITOR_COMPONENT, $this)->run();
        //ToolComponent :: launch($this,RightsTool);
        //the launch method results in the default action of the toolcomponent, not the default action of the rights tool!
        //this needs to be looked at
    }

    function get_available_rights()
    {
        return WeblcmsRights :: get_available_rights();
    }
}
?>