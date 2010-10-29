<?php
namespace application\weblcms\tool\wiki;

use application\weblcms\WeblcmsDataManager;
use application\weblcms\Tool;
use repository\ComplexDisplay;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use application\weblcms\ToolComponent;
use common\libraries\DelegateComponent;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Path;
use repository\content_object\wiki\WikiComplexDisplaySupport;

require_once Path :: get_repository_content_object_path() . 'wiki/php/display/wiki_complex_display_support.class.php';

/**
 * $Id: wiki_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.wiki.component
 */

class WikiToolComplexDisplayComponent extends WikiTool implements DelegateComponent, WikiComplexDisplaySupport
{
    private $publication;

    function run()
    {
        $publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $publication_id);

        $this->publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);

        ComplexDisplay :: launch($this->publication->get_content_object()->get_type(), $this);
    }

    function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    function get_publication()
    {
        return $this->publication;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('WikiToolBrowserComponent')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('WikiToolViewerComponent')));
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }

    function get_page_statistics_reporting_template_name()
    {
        return 'wiki_page_reporting_template';
    }

    function get_statistics_reporting_template_name()
    {
        return 'wiki_reporting_template';
    }
}
?>