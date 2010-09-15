<?php

class RightsToolRightsEditorComponent extends RightsTool implements DelegateComponent
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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('RightsEditorToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        array(Tool :: PARAM_PUBLICATION_ID);
    }
}
?>