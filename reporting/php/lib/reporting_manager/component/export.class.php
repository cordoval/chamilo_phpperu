<?php
/**
 * $Id: export.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component
 * @author Michael Kyndt
 */

class ReportingManagerExportComponent extends ReportingManager implements AdministrationComponent, DelegateComponent
{

    function run()
    {
        $rte = new ReportingExporter($this);
        
        if (Request :: get(ReportingManager :: PARAM_REPORTING_BLOCK_ID))
        {
			$rbi = Request :: get(ReportingManager :: PARAM_REPORTING_BLOCK_ID);
        }
        else 
        {
            if (Request :: get(ReportingManager :: PARAM_TEMPLATE_ID))
            {
                $ti = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);
            }
        }
		//$params = Request :: get(ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS);
        //$params = unserialize(base64_decode($params));
        /*$_SESSION[ReportingManager :: PARAM_REPORTING_PARENT] = $this;*/
        //$params['export'] = true;
        $export = Request :: get(ReportingManager :: PARAM_EXPORT_TYPE);
        
        $rtv = new ReportingExporter($this);
        $rtv->add_export($export);
        $rtv->export();
        //$rte->export();
        /*if (isset($rbi))
        {
            $rte->export_reporting_block($rbi, $export, $params);
        }
        else 
            if (isset($ti))
            {
                $rte->export_template($ti, $export, $params);
            }*/
    } //run
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('ReportingManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('reporting_viewer');
    }
    
    function get_additional_parameters()
    {
    	return array(ReportingManager :: PARAM_TEMPLATE_ID, ReportingManager :: PARAM_REPORTING_BLOCK_ID, ReportingManager :: PARAM_EXPORT_TYPE);
    }
}
?>