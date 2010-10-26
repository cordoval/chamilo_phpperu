<?php
namespace application\weblcms\tool\maintenance;

/**
 * $Id: maintenance_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance
 */

require_once dirname(__FILE__) . '/inc/maintenance_wizard.class.php';
/**
 * This tool implements some maintenance tools for a course.
 * It gives a course administrator the possibilities to copy course content,
 * remove publications from the course, create & restore backups,...
 */
class MaintenanceTool extends Tool
{
    const DEFAULT_ACTION = self :: ACTION_VIEW;

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
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