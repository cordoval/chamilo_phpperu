<?php
namespace application\weblcms\tool\announcement;

use application\weblcms\WeblcmsRights;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use application\weblcms\WeblcmsManager;
use application\weblcms\Tool;
use application\weblcms\ToolComponent;
use common\libraries\DelegateComponent;
use common\libraries\Translation;

class AnnouncementToolRightsEditorComponent extends AnnouncementTool implements DelegateComponent
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('AnnouncementToolBrowserComponent')));
        if (Request :: get(WeblcmsManager :: PARAM_PUBLICATION))
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('AnnouncementToolViewerComponent')));
        }
    }

    function get_additional_parameters()
    {
        array(Tool :: PARAM_PUBLICATION_ID);
    }

}

?>