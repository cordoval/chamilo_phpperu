<?php
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
        $classname = Request :: get(ReportingManager :: PARAM_TEMPLATE_NAME);
        $this->set_parameter(ReportingManager :: PARAM_TEMPLATE_NAME, $classname);

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses reporting');

        if (Request :: get('pcattree') != null && Request :: get('pcattree') > 0)
        {
            $this->add_pcattree_breadcrumbs(Request :: get('pcattree'), $trail);
        }

        $user = Request :: get('user_id');

        if (! empty($user) && Request :: get('template_name') == 'course_student_tracker_detail_reporting_template')
        {
            $user = DatabaseUserDataManager :: get_instance()->retrieve_user($user);
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'user_details', 'users' => $user)), $user->get_firstname() . ' ' . $user->get_lastname()));
        }

        if (Request :: get('cid') != null)
        {
            $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item(Request :: get('cid'));
            $wp = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref());
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Request :: get('tool') == 'learning_path' ? 'view_clo' : 'view', 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'))), $wp->get_title()));

        }

        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_REPORTING_TEMPLATE, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'), 'template_name' => Request :: get('template_name'))), Translation :: get('Reporting')));

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
}
?>