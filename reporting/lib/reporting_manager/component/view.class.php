<?php
/**
 * $Id: view.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component
 * @author Michael Kyndt
 */

class ReportingManagerViewComponent extends ReportingManager
{

    //private $template;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $template = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => ReportingManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Reporting')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
        
        $rtv = new ReportingViewer($this);
        $rtv->add_template_by_id($template);
        $rtv->set_breadcrumb_trail($trail);
        
        $rtv->run();
    } //run
}
?>