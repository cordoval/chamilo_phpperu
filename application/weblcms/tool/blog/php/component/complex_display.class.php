<?php
namespace application\weblcms\tool\blog;

use repository\content_object\blog\BlogComplexDisplaySupport;
use repository\ComplexDisplay;

use application\weblcms\Tool;
use application\weblcms\ToolComponent;
use application\weblcms\WeblcmsDataManager;

use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\DelegateComponent;
use common\libraries\Translation;

/**
 * $Id: blog_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.blog.component
 */

/**
 * Represents the view component for the assessment tool.
 *
 */
class BlogToolComplexDisplayComponent extends BlogTool implements
        DelegateComponent,
        BlogComplexDisplaySupport
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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('BlogToolBrowserComponent')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                Tool :: PARAM_ACTION => Tool :: ACTION_VIEW,
                Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('BlogToolViewerComponent')));

    }

    function get_additional_parameters()
    {
        return array(
                Tool :: PARAM_PUBLICATION_ID);
    }
}

?>