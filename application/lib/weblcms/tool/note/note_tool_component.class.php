<?php
/**
 * $Id: note_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.note
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all note tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class NoteToolComponent extends ToolComponent
{

    static function factory($component_name, $note_tool)
    {
        return parent :: factory('Note', $component_name, $note_tool);
    }

    function run()
    {}
}