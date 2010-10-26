<?php
namespace application\weblcms\tool\note;

use application\weblcms\ToolComponent;
use common\libraries\DelegateComponent;

class NoteToolRightsEditorComponent extends NoteTool implements DelegateComponent
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_available_rights()
    {
        return WeblcmsRights :: get_available_rights();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('NoteToolBrowserComponent')));
        if (Request :: get(WeblcmsManager :: PARAM_PUBLICATION))
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request::get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('NoteToolViewerComponent')));
        }
    }

    function get_additional_parameters()
    {
        array(Tool :: PARAM_PUBLICATION_ID);
    }

}

?>