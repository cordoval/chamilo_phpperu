<?php
namespace application\weblcms\tool\wiki;

use application\weblcms\Tool;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use application\weblcms\ToolComponent;
use common\libraries\Translation;
use repository\content_object\wiki\WikiComplexDisplaySupport;

require_once Path :: get_repository_content_object_path() . 'wiki/php/display/wiki_complex_display_support.class.php';

class WikiToolViewerComponent extends WikiTool implements WikiComplexDisplaySupport
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('WikiToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }
}

?>