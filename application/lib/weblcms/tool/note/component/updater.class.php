<?php
class NoteToolUpdaterComponent extends NoteTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('NoteToolBrowserComponent')));
                $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool::PARAM_PUBLICATION_ID))), Translation :: get('NoteToolViewerComponent')));

    }
    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }
}
?>
