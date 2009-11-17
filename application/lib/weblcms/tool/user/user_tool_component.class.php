<?php
/**
 * $Id: user_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all announcement tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class UserToolComponent extends ToolComponent
{

    static function factory($component_name, $announcement_tool)
    {
        return parent :: factory('User', $component_name, $announcement_tool);
    }
}