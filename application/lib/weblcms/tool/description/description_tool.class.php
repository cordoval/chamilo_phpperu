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
        
        if ($component)
            return;
        
        switch ($action)
        {
            case self :: ACTION_VIEW_DESCRIPTIONS :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_DELETE :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_UPDATE :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_MOVE_UP :
                $component = $this->create_component('MoveUp');
                break;
            case self :: ACTION_MOVE_DOWN :
                $component = $this->create_component('MoveDown');
                break;
            case self :: ACTION_TOGGLE_VISIBILITY :
                $component = $this->create_component('ToggleVisibility');
                break;
            case self :: ACTION_PUBLISH :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_PUBLISH_INTRODUCTION :
                $component = $this->create_component('IntroductionPublisher');
                break;
            case self :: ACTION_SHOW_PUBLICATION:
            	$component = $this->create_component('ShowPublication');
                break;
            case self :: ACTION_HIDE_PUBLICATION:
            	$component = $this->create_component('HidePublication');
                break;
            case self :: ACTION_EDIT_RIGHTS:
            	$component = $this->create_component('RightsEditor');
                break;
            default :
                $component = $this->create_component('Browser');
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