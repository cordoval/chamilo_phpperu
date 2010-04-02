<?php
/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.reporting.component
 */
/**
 * @author Michael Kyndt
 */

class ReportingToolViewerComponent extends ReportingToolComponent
{

    function run()
    {
        /*$classname = 'CourseStudentTrackerReportingTemplate';
        
        $params = Reporting :: get_params($this);
        $url = array();
        $url[Tool :: PARAM_ACTION] = 'view_reporting_template';
        $url['template_name'] = $classname;
        foreach ($params as $key => $param)
        {
            $url[$key] = $param;
        }
        //dump($url); dump($this->get_url($url));
        header('location:' . $this->get_url($url));*/
        
        
		$template_id = Reporting :: get_name_registration(Utilities :: camelcase_to_underscores('CourseStudentTrackerReportingTemplate'), WeblcmsManager::APPLICATION_NAME)->get_id();
        $rtv = new ReportingTemplateViewer($this);
 
        $trail = new BreadcrumbTrail();
        /*$trail->add_help('courses reporting');
        $trail->add(new Breadcrumb($this->get_url(array(Application::PARAM_ACTION => null, 'pcattree' => null, WeblcmsManager::PARAM_COURSE => null)), Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb($this->get_url(array(Application::PARAM_ACTION => WeblcmsManager::ACTION_VIEW_COURSE, 'pcattree' => null, WeblcmsManager::PARAM_TOOL => null)), WebLcmsDataManager :: get_instance()->retrieve_course(Request :: get(WeblcmsManager :: PARAM_COURSE))->get_name()));
        if ($trail->get_last() != new Breadcrumb($this->get_parent()->get_reporting_url($classname, $params), Translation :: get('Reporting')))
            $trail->add(new Breadcrumb($this->get_parent()->get_reporting_url($classname, $params), Translation :: get('Reporting')));
        */
        $this->display_header($trail, false, true);
        $rtv->show_reporting_template($template_id);
        $this->display_footer();

    }
}
?>