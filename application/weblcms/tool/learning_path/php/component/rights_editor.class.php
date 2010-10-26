<?php
namespace application\weblcms\tool\learning_path;

use application\weblcms\ToolComponent;
use common\libraries\DelegateComponent;
use common\libraries\Translation;

class LearningPathToolRightsEditorComponent extends LearningPathTool implements DelegateComponent
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('LearningPathToolBrowserComponent')));
        if (Request :: get(WeblcmsManager :: PARAM_PUBLICATION))
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request::get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('LearningPathToolViewerComponent')));
        }
    }

    function get_additional_parameters()
    {
        return array(RepoViewer::PARAM_ID, RepoViewer::PARAM_ACTION);
    }

}

?>