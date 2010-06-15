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

    static function factory($type, $tool_component)
    {
        $file = dirname(__FILE__) . '/component/' . $type . '.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ToolComponentTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = 'Tool' . Utilities :: underscores_to_camelcase($type) . 'Component';
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

    function get_application_component_path()
    {
    }

    function get_course()
    {
        return $this->get_parent()->get_course();
    }

    function get_course_id()
    {
        return $this->get_parent()->get_course_id();
    }

    function get_user_info($user_id)
    {
        return $this->get_parent()->get_user_info($user_id);
    }

    function get_tool_id()
    {
        return $this->get_parent()->get_tool_id();
    }

}