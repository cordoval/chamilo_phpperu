<?php
namespace application\weblcms\tool\description;

use application\weblcms\Tool;
use application\weblcms\ToolComponent;

use common\extensions\repo_viewer\RepoViewer;

use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Translation;
use common\libraries\Path;

/**
 * $Id: description_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.description.component
 */
require_once dirname(__FILE__) . '/../description_tool.class.php';
require_once Path :: get_application_path() . 'weblcms/php/lib/content_object_repo_viewer.class.php';
require_once Path :: get_application_path() . 'weblcms/php/lib/publisher/content_object_publisher.class.php';

class DescriptionToolPublisherComponent extends DescriptionTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('DescriptionToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(RepoViewer :: PARAM_ID, RepoViewer :: PARAM_ACTION);
    }
}
?>