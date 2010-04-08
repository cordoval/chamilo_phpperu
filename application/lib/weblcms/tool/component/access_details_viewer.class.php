<?php
/**
 * $Id: access_details_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */

/**
 * Description of reporting_template_viewerclass
 *
 * @author Soliber
 */

class ToolAccessDetailsViewerComponent extends ToolComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $rtv = new ReportingTemplateViewer($this);
        
        $classname = Request :: get(ReportingManager :: PARAM_TEMPLATE_NAME);
        
        $params = Reporting :: get_params($this);
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses reporting');
        
        if (Request :: get('pcattree') != null && Request :: get('pcattree') > 0)
            $this->add_pcattree_breadcrumbs(Request :: get('pcattree'), $trail);
            
        //        if(Request :: get(Tool :: PARAM_PUBLICATION_ID) != null && Request :: get('template_name')!='CourseStudentTrackerReportingTemplate' && Request :: get('template_name')!='CourseTrackerReportingTemplate')
        //        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Request :: get('tool')=='learning_path'?'view_clo':'view', 'display_action' => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID))->get_content_object()->get_title()));
        

        if (! empty($params['user_id']) && Request :: get('template_name') == 'CourseStudentTrackerDetailReportingTemplate')
        {
            $user = DatabaseUserDataManager :: get_instance()->retrieve_user($params['user_id']);
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'user_details', 'users' => $params['user_id'])), $user->get_firstname() . ' ' . $user->get_lastname()));
        }
        
        if (Request :: get('cid') != null)
        {
            $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item(Request :: get('cid'));
            $wp = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref());
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Request :: get('tool') == 'learning_path' ? 'view_clo' : 'view', 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'))), $wp->get_title()));
        
        }
        
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_REPORTING_TEMPLATE, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'), 'template_name' => Request :: get('template_name'))), Translation :: get('Reporting')));
        
        $this->display_header($trail, true);
        $rtv->show_reporting_template_by_name($classname, $params);
        $this->display_footer();
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