<?php
/**
 * $Id: forum_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.forum
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all forum tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class ForumToolComponent extends ToolComponent
{

    static function factory($component_name, $forum_tool)
    {
        return parent :: factory('Forum', $component_name, $forum_tool);
    }
}