<?php
/**
 * $Id: document_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all announcement tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class DocumentToolComponent extends ToolComponent
{

    static function factory($component_name, $announcement_tool)
    {
        return parent :: factory('Document', $component_name, $announcement_tool);
    }
}