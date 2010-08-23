<?php

/**
 * $Id: user_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package user
 */

require_once dirname(__FILE__) . '/forum_manager/forum_manager.class.php';

class ForumRights
{
    const PUBLISH_RIGHT = '1';
    
    const TREE_TYPE_FORUM = 1;
    const TYPE_CATEGORY = 1;
    
    static function get_available_rights_for_categories()
    {
    	return array('Publish' => self :: PUBLISH_RIGHT);
    }
    
	static function create_location_in_forums_subtree($name, $identifier, $parent, $type = self :: TYPE_CATEGORY)
    {
    	return RightsUtilities :: create_location($name, ForumManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_FORUM);
    }
    
    static function get_forums_subtree_root()
    {
    	return RightsUtilities :: get_root(ForumManager :: APPLICATION_NAME, self :: TREE_TYPE_FORUM, 0);
    }
    
	static function get_forums_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(ForumManager :: APPLICATION_NAME, self :: TREE_TYPE_FORUM, 0);
    }
    
    static function get_location_id_by_identifier_from_forums_subtree($identifier, $type = self :: TYPE_CATEGORY)
    {
    	return RightsUtilities :: get_location_id_by_identifier(ForumManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_FORUM);
    }
    
	static function get_location_by_identifier_from_forums_subtree($identifier, $type = self :: TYPE_CATEGORY)
    {
    	return RightsUtilities :: get_location_by_identifier(ForumManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_FORUM);
    }
    
	static function is_allowed_in_forums_subtree($right, $location, $type = self :: TYPE_CATEGORY)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, ForumManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_FORUM);
    }
    
    static function create_forums_subtree_root_location()
    {
    	return RightsUtilities :: create_location('forums_tree', ForumManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_FORUM);
    }
}
?>