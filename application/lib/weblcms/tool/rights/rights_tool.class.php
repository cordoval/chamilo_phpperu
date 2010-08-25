<?php
/**
 * $Id: rights_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.rights
 */

/**
 * This tool allows a user to manage rights in his or her course.
 */
class RightsTool extends Tool
{
    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case self :: ACTION_EDIT_RIGHTS :
            default:
                $component = $this->create_component('RightsEditor');
                break;
        }
        $component->run();
    }

	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}

}
?>