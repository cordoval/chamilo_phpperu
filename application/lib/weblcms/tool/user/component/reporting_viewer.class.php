<?php
/**
 * $Id: user_details.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */
require_once dirname(__FILE__) . '/../user_tool.class.php';

class UserToolReportingViewerComponent extends UserTool
{

    function run()
    {
        $template = Utilities::camelcase_to_underscores('CourseStudentTrackerDetailReportingTemplate');
        $rtv = new ReportingViewer($this);
        $rtv->add_template_by_name($template, WeblcmsManager::APPLICATION_NAME);
		$rtv->show_all_blocks();
        $rtv->run();        
    }

}
?>