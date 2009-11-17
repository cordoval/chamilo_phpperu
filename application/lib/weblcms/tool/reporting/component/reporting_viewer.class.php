<?php
/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.reporting.component
 */
/**
 * @author Michael Kyndt
 */

class ReportingToolViewerComponent extends ReportingToolComponent
{

    function run()
    {
        echo 'bus';
        $classname = 'CourseStudentTrackerReportingTemplate';
        
        $params = Reporting :: get_params($this);
        $url = array();
        $url[Tool :: PARAM_ACTION] = 'view_reporting_template';
        $url['template_name'] = $classname;
        foreach ($params as $key => $param)
        {
            $url[$key] = $param;
        }
        
        header('location:' . $this->get_url($url));
    }
}
?>