<?php
namespace application\weblcms\tool\survey;

use application\weblcms\WeblcmsDataManager;
use repository\RepositoryDataManager;

/**
 * $Id: export.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_results_export_form
 */
require_once dirname(__FILE__) . '/results_exporters/export_csv.class.php';
require_once dirname(__FILE__) . '/results_exporters/export_xml.class.php';
require_once dirname(__FILE__) . '/results_exporters/export_pdf.class.php';

abstract class ResultsExport
{
    const FILETYPE_CSV = 'csv';
    const FILETYPE_PDF = 'pdf';
    const FILETYPE_XML = 'xml';

    protected $wdm;
    protected $rdm;

    function ResultsExport()
    {
        $this->wdm = WeblcmsDataManager :: get_instance();
        $this->rdm = RepositoryDataManager :: get_instance();
    }

    function export_results($type, $id)
    {
        if ($type == 'assessment')
        {
            $data = $this->export_publication_id($id);
        }
        else
        {
            $data = $this->export_user_assessment_id($id);
        }
        return $data;
    }

    abstract function export_publication_id($id);

    abstract function export_user_assessment_id($id);

    function factory($filetype)
    {
        switch ($filetype)
        {
            case self :: FILETYPE_XML :
                return new ResultsXmlExport();
            case self :: FILETYPE_CSV :
                return new ResultsCsvExport();
            case self :: FILETYPE_PDF :
                return new ResultsPdfExport();
            default :
                return null;
        }
        return null;
    }
}
?>