<?php
namespace application\weblcms\tool\geolocation;

/**
 * $Id: geolocation_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation
 */

/**
 * This tool allows a user to publish announcements in his or her course.
 */
class GeolocationTool extends Tool
{

    static function get_allowed_types()
    {
        return array(PhysicalLocation :: get_type_name());
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function get_available_browser_types()
    {
        return array(ContentObjectPublicationListRenderer :: TYPE_TABLE);
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }
}
?>