<?php
namespace application\weblcms\tool\assessment;

use application\weblcms\WeblcmsDataManager;
use common\libraries\Filesystem;
use common\libraries\Request;
use repository\ContentObjectExport;

/**
 * $Id: assessment_qti_export.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */
class AssessmentToolQtiExporterComponent extends AssessmentTool
{

    function run()
    {
        $pid = Request :: get(AssessmentTool :: PARAM_PUBLICATION_ID);
        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $pid))
        {
            Display :: not_allowed();
            return;
        }

        $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($pid);
        $assessment = $publication->get_content_object();
        $exporter = ContentObjectExport :: factory('qti', $assessment);
        $path = $exporter->export_content_object();

        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Cache-Control: public');
        header('Pragma: no-cache');
        header('Content-type: application/octet-stream');
        header('Content-length: ' . filesize($path));

        if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
        {
            header('Content-Disposition: filename= ' . basename($path));
        }
        else
        {
            header('Content-Disposition: attachment; filename= ' . basename($path));
        }

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
        {
            header('Pragma: ');
            header('Cache-Control: ');
            header('Cache-Control: public'); // IE cannot download from sessions without a cache
        }

        header('Content-Description: ' . basename($path));
        header('Content-transfer-encoding: binary');
        $fp = fopen($path, 'r');
        fpassthru($fp);
        fclose($fp);
        Filesystem :: remove($path);
    }
}
?>