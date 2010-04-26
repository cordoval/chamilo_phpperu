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
        
        $params[ReportingManager::PARAM_TEMPLATE_ID] = $template;
        
        $trail = new BreadcrumbTrail();
        
        if ($this->get_user()->is_platform_admin())
        {
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        }
        else
        {
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courses')));
        }
        
        $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER, WeblcmsManager :: PARAM_COURSE => null)), Translation :: get('CourseList')));
        
        if ($trail->get_last() != new Breadcrumb($this->get_reporting_url($params), Translation :: get('Reporting')))
        {
            $trail->add(new Breadcrumb($this->get_reporting_url($params), Translation :: get('Reporting')));
        }

  		$rtv = new ReportingViewer($this);
        $rtv->add_template_by_id($template);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->show_all_blocks();
        
        $rtv->run();
    }
}
?>