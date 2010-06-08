<?php
/**
 * $Id: note_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.note
 */

require_once dirname(__FILE__) . '/note_tool_component.class.php';
/**
 * This tool allows a user to publish notes in his or her course.
 */
class NoteTool extends Tool
{
    const ACTION_VIEW_NOTES = 'view';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        $component = parent :: run();
        
        if ($component)
        {
            return;
        }
        
        switch ($action)
        {
            case self :: ACTION_VIEW_NOTES :
                $component = NoteToolComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_PUBLISH :
                $component = NoteToolComponent :: factory('Publisher', $this);
                break;
            
            default :
                $component = NoteToolComponent :: factory('Viewer', $this);
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Note :: get_type_name());
    }
}
?>