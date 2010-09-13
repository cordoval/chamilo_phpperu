<?php
/**
 * $Id: view.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component
 * @author Michael Kyndt
 */

class ReportingManagerViewComponent extends ReportingManager implements AdministrationComponent, DelegateComponent
{

    //private $template;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $template = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);

        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_id($template);
        $rtv->set_breadcrumb_trail();

        $rtv->run();
    } //run
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('ReportingManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('reporting_viewer');
    }
    
    function get_additional_parameters()
    {
    	return array(ReportingManager :: PARAM_TEMPLATE_ID);
    }
}
?>