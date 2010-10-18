<?php
/**
 * $Id: results_exporter.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/results_export_form/results_export_form.class.php';
require_once dirname(__FILE__) . '/results_export_form/export.class.php';

class AssessmentManagerResultsExporterComponent extends AssessmentManager
{

    function run()
    {
        if (Request :: get('tid'))
        {
            $id = Request :: get('tid');
            $url = $this->get_results_exporter_url($id);
            $type = 'user_assessment';
            $export_form = new AssessmentResultsExportForm($url);
        }
        if ($export_form->validate())
        {
            $values = $export_form->exportValues();
            $filetype = $values['filetype'];
            $this->export($type, $id, $filetype);
        }
        else
        {
            $this->display_header(null, true);
            echo $export_form->toHtml();
            $this->display_footer();
        }
    }

    function export($type, $id, $filetype)
    {
        
        $results_exporter = ResultsExport :: factory($filetype);
        
        $data = $results_exporter->export_results($type, $id);
        $exporter = Export :: factory($filetype, $data);
        $exporter->set_filename('export_' . $type . $id);
        $exporter->send_to_browser();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('assessment_results_exporter');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('AssessmentManagerBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array('tid');
    }

}
?>