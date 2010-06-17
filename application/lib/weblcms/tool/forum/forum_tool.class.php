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
    const ACTION_MANAGE_CATEGORIES = 'manage_categories';
    
	const ACTION_EDIT_FORUM = 'edit';
    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
xdebug_break();
        switch ($action)
        {
            
            case self :: ACTION_PUBLISH_FORUM :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_BROWSE_FORUMS :
            	$component = $this->create_component('Browser');
                break;
            case self :: ACTION_VIEW_FORUM :
            	$component = $this->create_component('Viewer');
                break;
            case self :: ACTION_MANAGE_CATEGORIES:
            	$component = $this->create_component('CategoryManager');
                break;
            case self :: ACTION_UPDATE :
            	$component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE :
            	$component = $this->create_component('Deleter');
                break;
            case self :: ACTION_TOGGLE_VISIBILITY:
            	$component = $this->create_component('ToggleVisibility');
                break;
            case self :: ACTION_MOVE_DOWN:
            	$component = $this->create_component('MoveDown');
                break;
            case self :: ACTION_MOVE_UP:
            	$component = $this->create_component('MoveUp');
                break;
            case self :: ACTION_MOVE_TO_CATEGORY:
            	$component = $this->create_component('CategoryMover');
                break;
            default :
                $component = $this->create_component('Browser');
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