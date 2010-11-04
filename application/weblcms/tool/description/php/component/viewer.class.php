<?php
namespace application\weblcms\tool\description;

use repository\content_object\introduction\Introduction;
use repository\ContentObject;
use repository\RepositoryDataManager;

use application\weblcms\ContentObjectPublication;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsRights;
use application\weblcms\Tool;
use application\weblcms\ToolComponent;

use common\libraries\PatternMatchCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\SubselectCondition;
use common\libraries\ToolbarItem;
use common\libraries\Display;
use common\libraries\Theme;
use common\libraries\OrCondition;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Translation;

class DescriptionToolViewerComponent extends DescriptionTool
{

    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        $component->run();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('DescriptionToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }
}
?>