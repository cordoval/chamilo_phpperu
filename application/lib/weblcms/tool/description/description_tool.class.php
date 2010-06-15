<?php
/**
 * $Id: description_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.description
 */

/**
 * This tool allows a user to publish descriptions in his or her course.
 */
class DescriptionTool extends Tool
{
    const ACTION_VIEW_DESCRIPTIONS = 'view';

    // Inherited.
    function run()
    {
        $action = $this->get_action();
        $component = parent :: run();
        
        if ($component)
            return;
        
        switch ($action)
        {
            case self :: ACTION_VIEW_DESCRIPTIONS :
                $component = DescriptionToolComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_PUBLISH :
                $component = DescriptionToolComponent :: factory('Publisher', $this);
                break;
            default :
                $component = DescriptionToolComponent :: factory('Viewer', $this);
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Description :: get_type_name());
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>