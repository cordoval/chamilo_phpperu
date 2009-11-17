<?php
/**
 * $Id: reporting.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */

class WeblcmsManagerReportingComponent extends WeblcmsManagerComponent
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
        $trail->add(new Breadcrumb($this->get_url(array('go' => null, 'pcattree' => null, 'course' => null)), Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb($this->get_url(array('go' => 'courseviewer', 'pcattree' => null, 'tool' => null)), WebLcmsDataManager :: get_instance()->retrieve_course(Request :: get('course'))->get_name()));
        if ($trail->get_last() != new Breadcrumb($this->get_parent()->get_reporting_url($classname, $params), Translation :: get('Reporting')))
            $trail->add(new Breadcrumb($this->get_parent()->get_reporting_url($classname, $params), Translation :: get('Reporting')));
        
        $this->display_header($trail, false, true);
        $rtv->show_reporting_template_by_name($classname, $params);
        $this->display_footer();
    }
}
?>