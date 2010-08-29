<?php
/**
 * $Id: tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool
 */

/**
==============================================================================
 * This is the base class component for all tool components used in applications.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
==============================================================================
 */

abstract class ToolComponent extends SubManager
{
    const ACTION_VIEW = 'viewer';
    const ACTION_BROWSE = 'browser';
    const ACTION_PUBLISH = 'publisher';
    const ACTION_UPDATE = 'updater';
    const ACTION_DELETE = 'deleter';
    const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
    const ACTION_MOVE = 'mover';
    
    const MOVE_TO_CATEGORY_COMPONENT = 'category_mover';
    const INTRODUCTION_PUBLISHER_COMPONENT = 'introduction_publisher';
    const MANAGE_CATEGORIES_COMPONENT = 'category_manager';
    const VIEW_REPORTING_COMPONENT = 'reporting_viewer';
    const BUILD_COMPLEX_CONTENT_OBJECT_COMPONENT = 'complex_builder';
    const DISPLAY_COMPLEX_CONTENT_OBJECT_COMPONENT = 'complex_display';
    const RIGHTS_EDITOR_COMPONENT = 'rights_editor';

    static function factory($type, $tool_component)
    {
        $file = dirname(__FILE__) . '/component/' . $type . '.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ToolComponentTypeDoesNotExist', array('type' => $type)));
        }
        
        require_once $file;
        
        $class = 'ToolComponent' . Utilities :: underscores_to_camelcase($type) . 'Component';
        return new $class($tool_component);
    }

    /**
     * Check if the current user has a given right in this tool
     * @param int $right
     * @return boolean True if the current user has the right
     */
    function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }

    /**
     * @see WeblcmsManager :: get_last_visit_date()
     */
    function get_last_visit_date()
    {
        return $this->get_parent()->get_last_visit_date();
    }

    function get_path($path_type)
    {
        return $this->get_parent()->get_path($path_type);
    }

    function perform_requested_actions()
    {
        return $this->get_parent()->perform_requested_actions();
    }

    function get_categories($list = false)
    {
        return $this->get_parent()->get_categories($list);
    }

    function get_category($id)
    {
        return $this->get_parent()->get_category($id);
    }

    function display_introduction_text($introduction_text)
    {
        return $this->get_parent()->display_introduction_text($introduction_text);
    }

    function get_access_details_toolbar_item($parent)
    {
        return $this->get_parent()->get_access_details_toolbar_item($parent);
    }

    function get_allowed_types()
    {
        return $this->get_parent()->get_allowed_types();
    }

    function get_complex_builder_url($pid)
    {
        return $this->get_parent()->get_complex_builder_url($pid);
    }

    function get_complex_display_url($pid)
    {
        return $this->get_parent()->get_complex_display_url($pid);
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function get_course()
    {
        return $this->get_parent()->get_course();
    }

    function get_course_id()
    {
        return $this->get_parent()->get_course_id();
    }

    function get_course_groups()
    {
        return $this->get_parent()->get_course_groups();
    }

    function get_user_info($user_id)
    {
        return $this->get_parent()->get_user_info($user_id);
    }

    function get_tool_id()
    {
        return $this->get_parent()->get_tool_id();
    }

    function display_header()
    {
        return $this->get_parent()->display_header();
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
        return Tool :: DEFAULT_ACTION;
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
        return Tool :: PARAM_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application, false);
    }
}