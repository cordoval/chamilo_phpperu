<?php
/**
 * $Id: reporting_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.reporting
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';

class ReportingToolComponent extends ToolComponent
{

    static function factory($component_name, $announcement_tool)
    {
        return parent :: factory('Reporting', $component_name, $announcement_tool);
    }
}