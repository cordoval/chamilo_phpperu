<?php
/**
 * $Id: forum_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.forum
 */

/**
 * This tool allows a user to publish forums in his or her course.
 */
class ForumTool extends Tool
{
    const ACTION_BROWSE_FORUMS = 'browse';
    const ACTION_VIEW_FORUM = 'view';
    const ACTION_PUBLISH_FORUM = 'publish';
    const ACTION_MANAGE_CATEGORIES = 'manage_forum_categories';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        $component = parent :: run();

        if ($component)
        {
            return;
        }

        switch ($action)
        {
            case self :: ACTION_PUBLISH_FORUM :
                $component = ForumToolComponent :: factory('Publisher', $this);
                break;
            case self :: ACTION_BROWSE_FORUMS :
                $component = ForumToolComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_VIEW_FORUM :
                $component = ForumToolComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_MANAGE_CATEGORIES:
            	$component = ForumToolComponent :: factory('CategoryManager', $this);
                break;
            default :
                $component = ForumToolComponent :: factory('Browser', $this);
        }
        $component->run();
    }

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

	function is_category_management_enabled()
	{
	    return true;
	}
}
?>