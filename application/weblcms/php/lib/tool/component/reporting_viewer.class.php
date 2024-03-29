<?php
namespace application\weblcms;

use common\extensions\reporting_viewer\ReportingViewer;

use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;

use reporting\ReportingManager;
use repository\RepositoryDataManager;
use user\UserDataManager;

/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */

/**
 * Description of reporting_template_viewerclass
 *
 * @author Soliber
 */
class ToolComponentReportingViewerComponent extends ToolComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        //TODO :: what users are allowed to view reports?
//        $course = $this->get_tool_browser()->get_course();
//        if(!$this->get_user()->is_platform_admin() && $course->is_course_admin($this->get_user()))
//        {
//
//        }
        $classname = Request :: get(ReportingManager :: PARAM_TEMPLATE_NAME);
        $this->set_parameter(ReportingManager :: PARAM_TEMPLATE_NAME, $classname);

        $trail = BreadcrumbTrail :: get_instance();

        $user = Request :: get('user_id');

        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name($classname, WeblcmsManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->show_all_blocks();

        $rtv->run();
    }

    private function add_pcattree_breadcrumbs($pcattree, &$trail)
    {
        $cat = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication_category($pcattree);
        $categories[] = $cat;
        while ($cat->get_parent() != 0)
        {
            $cat = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication_category($cat->get_parent());
            $categories[] = $cat;
        }
        $categories = array_reverse($categories);
        foreach ($categories as $categorie)
        {
            $trail->add(new Breadcrumb($this->get_url(array('pcattree' => $categorie->get_id())), $categorie->get_name()));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        if (Request :: get('pcattree') != null && Request :: get('pcattree') > 0)
        {
            $this->add_pcattree_breadcrumbs(Request :: get('pcattree'), $breadcrumbtrail);
        }
        if (! empty($user) && Request :: get(Tool :: PARAM_TEMPLATE_NAME) == 'course_student_tracker_detail_reporting_template')
        {
            $user = UserDataManager :: get_instance()->retrieve_user($user);
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'user_details', 'users' => $user)), $user->get_firstname() . ' ' . $user->get_lastname()));
        }

        if (Request :: get('cid') != null)
        {
            $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item(Request :: get('cid'));
            $wp = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref());
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                    Tool :: PARAM_ACTION => Request :: get('tool') == 'learning_path' ? 'view_clo' : 'view', 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'))), $wp->get_title()));
        }
        $breadcrumbtrail->add_help('weblcms_tool_reporting_viewer');
    }

}

?>