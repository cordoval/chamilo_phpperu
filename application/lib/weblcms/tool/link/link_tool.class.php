<?php
/**
 * $Id: link_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.link
 */

/**
 * This tool allows a user to publish links in his or her course.
 */
class LinkTool extends Tool
{


    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case self :: ACTION_VIEW :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_PUBLISH :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_UPDATE:
            	$component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE:
            	$component = $this->create_component('Deleter');
                break;
            case self :: ACTION_TOGGLE_VISIBILITY:
            	$component = $this->create_component('ToggleVisibility');
                break;
            case self :: ACTION_MOVE_UP:
            	$component = $this->create_component('MoveUp');
                break;
            case self :: ACTION_MOVE_DOWN:
            	$component = $this->create_component('MoveDown');
                break;
            case self :: ACTION_PUBLISH_INTRODUCTION:
            	$component = $this->create_component('IntroductionPublisher');
                break;
            case self :: ACTION_MANAGE_CATEGORIES:
            	$component = $this->create_component('CategoryManager');
                break;
            case self :: ACTION_VIEW_REPORTING_TEMPLATE:
            	$component = $this->create_component('ReportingViewer');
                break;
            default :
                $component = $this->create_component('Browser');
            	break;
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Link :: get_type_name());
    }

	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}

	function is_category_management_enabled()
	{
	    return true;
	}
}
?>