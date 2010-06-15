<?php
/**
 * $Id: note_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.note
 */

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
//        $component = parent :: run();
        
        if ($component)
        {
            return;
        }
        
        switch ($action)
        {
            case self :: ACTION_VIEW_NOTES :
//                $component = NoteToolComponent :: factory('Viewer', $this);
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_PUBLISH :
//                $component = NoteToolComponent :: factory('Publisher', $this);
                $component = $this->create_component('Publisher');
                break;
            default :
//                $component = NoteToolComponent :: factory('Viewer', $this);
                $component = $this->create_component('Viewer');
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Note :: get_type_name());
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>