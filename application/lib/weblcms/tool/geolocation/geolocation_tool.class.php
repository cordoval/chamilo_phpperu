<?php
/**
 * $Id: geolocation_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation
 */

require_once dirname(__FILE__) . '/geolocation_tool_component.class.php';
/**
 * This tool allows a user to publish announcements in his or her course.
 */
class GeolocationTool extends Tool
{
    const ACTION_BROWSE = 'browse';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        $component = parent :: run();
        
        if ($component)
            return;
        
        switch ($action)
        {
            case self :: ACTION_BROWSE :
                $component = GeolocationToolComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_PUBLISH :
                $component = GeolocationToolComponent :: factory('Publisher', $this);
                break;
            default :
                $component = GeolocationToolComponent :: factory('Browser', $this);
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(PhysicalLocation :: get_type_name());
    }
}
?>