<?php
namespace application\weblcms\tool\forum;

use application\weblcms\WeblcmsRights;
use application\weblcms\WeblcmsManager;
use application\weblcms\Tool;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use application\weblcms\ToolComponent;
use common\libraries\DelegateComponent;
use common\libraries\Translation;

class ForumToolRightsEditorComponent extends ForumTool implements DelegateComponent
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('ForumToolBrowserComponent')));
        if (Request :: get(WeblcmsManager :: PARAM_PUBLICATION))
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('ForumToolViewerComponent')));
        }
    }

    function get_additional_parameters()
    {
        array(Tool :: PARAM_PUBLICATION_ID);
    }

}

?>