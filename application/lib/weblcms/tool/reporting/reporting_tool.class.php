<?php
/**
 * $Id: reporting_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.reporting
 * @author Michael Kyndt
 */

class ReportingTool extends Tool
{
    const ACTION_VIEW_REPORT = 'view';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_VIEW_REPORT :
                $component = $this->create_component('Viewer');
                break;
            default :
                $component = $this->create_component('Viewer');
        }
        $component->run();
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>