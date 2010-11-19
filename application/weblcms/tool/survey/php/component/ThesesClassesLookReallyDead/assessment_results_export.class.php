<?php
//namespace application\weblcms\tool\survey;
//
//use application\weblcms\WeblcmsRights;
//use application\weblcms\Tool;
//use common\libraries\Display;
//use common\libraries\Breadcrumb;
//use common\libraries\BreadcrumbTrail;
//use common\libraries\Request;
//use common\libraries\Translation;
//
///**
// * $Id: assessment_results_export.class.php 216 2009-11-13 14:08:06Z kariboe $
// * @package application.lib.weblcms.tool.assessment.component
// */
//require_once dirname(__FILE__) . '/assessment_results_export_form/results_export_form.class.php';
//require_once dirname(__FILE__) . '/assessment_results_export_form/export.class.php';
//
//class AssessmentToolResultsExportComponent extends AssessmentToolComponent
//{
//
//    private $rdm;
//    private $wdm;
//
//    function run()
//    {
//        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
//        {
//            Display :: not_allowed();
//            return;
//        }
//        $trail = BreadcrumbTrail :: get_instance();
//        $trail->add_help('courses assessment tool');
//        $toolbar = $this->get_toolbar();
//
//        if (Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT))
//        {
//            $id = Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT);
//            $trail->add(new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $id)), Translation :: get('ExportResults')));
//            $type = 'user_assessment';
//            $export_form = new AssessmentResultsExportForm($this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $id)));
//        }
//        else
//        {
//            $id = Request :: get(AssessmentTool :: PARAM_PUBLICATION_ID);
//            $trail->add(new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_RESULTS, AssessmentTool :: PARAM_PUBLICATION_ID => $id)), Translation :: get('ExportResults')));
//            $type = Assessment :: get_type_name();
//            $export_form = new AssessmentResultsExportForm($this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_RESULTS, AssessmentTool :: PARAM_PUBLICATION_ID => $id)));
//        }
//
//        if ($export_form->validate())
//        {
//            $values = $export_form->exportValues();
//            $filetype = $values['filetype'];
//            $this->export($type, $id, $filetype);
//        }
//        else
//        {
//            $this->display_header();
//            echo $toolbar->as_html();
//            echo $export_form->toHtml();
//            $this->display_footer();
//        }
//    }
//
//    function export($type, $id, $filetype)
//    {
//        $results_exporter = ResultsExport :: factory($filetype);
//
//        $data = $results_exporter->export_results($type, $id);
//        $exporter = Export :: factory($filetype, $data);
//        $exporter->set_filename('export_' . $type . $id);
//        $exporter->send_to_browser();
//    }
//
//}
?>