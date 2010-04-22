<?php
/**
 * $Id: reporting.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */

class WeblcmsManagerReportingComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {      
        $template = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses reporting');
        $trail->add(new Breadcrumb($this->get_url(array(Application::PARAM_ACTION => null, 'pcattree' => null, WeblcmsManager::PARAM_COURSE => null)), Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb($this->get_url(array(Application::PARAM_ACTION => WeblcmsManager::ACTION_VIEW_COURSE, 'pcattree' => null, WeblcmsManager::PARAM_TOOL => null)), WebLcmsDataManager :: get_instance()->retrieve_course(Request :: get(WeblcmsManager :: PARAM_COURSE))->get_name()));
        if ($trail->get_last() != new Breadcrumb($this->get_parent()->get_reporting_url($classname, $params), Translation :: get('Reporting')))
            $trail->add(new Breadcrumb($this->get_parent()->get_reporting_url($classname, $params), Translation :: get('Reporting')));
        
  		$rtv = new ReportingViewer($this);
        $rtv->add_template_by_id($template);
        $rtv->set_breadcrumb_trail($trail);
         $rtv->show_all_blocks();
        
        $rtv->run();
    }
}
?>