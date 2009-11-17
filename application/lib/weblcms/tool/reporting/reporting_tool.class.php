<?php
/**
 * $Id: reporting_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.reporting
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_tool_component.class.php';

class ReportingTool extends Tool
{
    const ACTION_VIEW_REPORT = 'view';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        $component = parent :: run();
        
        if ($component)
            return;
        
        switch ($action)
        {
            case self :: ACTION_VIEW_REPORT :
                $component = ReportingToolComponent :: factory('Viewer', $this);
                break;
            default :
                $component = ReportingToolComponent :: factory('Viewer', $this);
        }
        $component->run();
    }
}
?>