<?php

namespace application\assessment;

use application\weblcms\WeblcmsDataManager;
use repository\RepositoryDataManager;
use common\libraries\Path;
/**
 * $Id: export.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component.results_export_form
 */
require_once dirname(__FILE__) . '/result_exporters/export_csv.class.php';
require_once dirname(__FILE__) . '/result_exporters/export_xml.class.php';
require_once dirname(__FILE__) . '/result_exporters/export_pdf.class.php';
require_once Path :: get_web_application_path('weblcms') . Path :: CLASS_PATH . '/lib/weblcms_data_manager.class.php';

abstract class ResultsExport
{
    const FILETYPE_CSV = 'csv';
    const FILETYPE_PDF = 'pdf';
    const FILETYPE_XML = 'xml';
    
    protected $wdm;
    protected $rdm;

    function __construct()
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