<?php
/**
 * $Id: qti_exporter.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/../assessment_manager_component.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerQtiExporterComponent extends AssessmentManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get('assessment_publication');
        if (! $pid)
        {
            $this->not_allowed();
            exit();
        }
        
        $publication = $this->retrieve_assessment_publication($pid);
        
        $assessment = $publication->get_publication_object();
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