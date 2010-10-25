<?php
/**
 * $Id: forum_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.forum
 */

/**
 * This tool allows a user to publish forums in his or her course.
 */
class ForumTool extends Tool implements Categorizable
{
    const ACTION_BROWSE_FORUMS = 'browser';
    const ACTION_VIEW_FORUM = 'viewer';
    const ACTION_PUBLISH_FORUM = 'publisher';
    const ACTION_MANAGE_CATEGORIES = 'category_manager';
    const ACTION_CHANGE_LOCK = 'change_lock';

    static function get_allowed_types()
    {
        return array(Forum :: get_type_name());
    }

    static function get_subforum_parents($subforum_id)
    {
        $rdm = RepositoryDataManager :: get_instance();
        
        $parent = $rdm->retrieve_complex_content_object_item($subforum_id);
        while (! empty($parent))
        {
            $parents[] = $parent;
            $parent = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_REF, $parent->get_parent()))->as_array();
            $parent = $parent[0];
        }
        $parents = array_reverse($parents);
        
        return $parents;
    }

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