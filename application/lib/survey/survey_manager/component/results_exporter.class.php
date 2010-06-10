<?php
/**
 * $Id: results_exporter.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component
 */
require_once dirname(__FILE__) . '/../survey_manager.class.php';

require_once dirname(__FILE__) . '/results_export_form/results_export_form.class.php';
require_once dirname(__FILE__) . '/results_export_form/export.class.php';

class SurveyManagerResultsExporterComponent extends SurveyManager
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_VIEW_SURVEY_PUBLICATION_RESULTS)), Translation :: get('ViewResults')));
        $trail->add_help('courses survey tool');
        
        if (Request :: get('tid'))
        {
            $id = Request :: get('tid');
            $url = $this->get_results_exporter_url($id);
            $trail->add(new Breadcrumb($url, Translation :: get('ExportResults')));
            $type = 'user_survey';
            $export_form = new SurveyResultsExportForm($url);
        }
        if ($export_form->validate())
        {
            $values = $export_form->exportValues();
            $filetype = $values['filetype'];
            $this->export($type, $id, $filetype);
        }
        else
        {
            $this->display_header($trail, true);
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

}
?>