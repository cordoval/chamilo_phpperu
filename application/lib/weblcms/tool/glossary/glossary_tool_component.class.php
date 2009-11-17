<?php
/**
 * $Id: glossary_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.glossary
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all glossary tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class GlossaryToolComponent extends ToolComponent
{

    static function factory($component_name, $glossary_tool)
    {
        return parent :: factory('Glossary', $component_name, $glossary_tool);
    }
}