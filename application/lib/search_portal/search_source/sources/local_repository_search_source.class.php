<?php
/**
 * $Id: local_repository_search_source.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_source
 */
require_once dirname(__FILE__) . '/../search_source.class.php';

class LocalRepositorySearchSource extends SearchSource
{
    function retrieve_search_results($query, $offset = 0, $max_objects = -1, $user = null)
    {	
	  	return RepositoryDataManager :: get_instance()->retrieve_shared_content_objects($this->get_condition($query, $user), $offset, $max_objects, array(new ObjectTableOrder(ContentObject :: PROPERTY_TITLE)))->as_array();
    }
    
	function count_search_results($query, $user = null)
	{
		return RepositoryDataManager :: get_instance()->count_shared_content_objects($this->get_condition($query, $user));
	}
	
	private function get_condition($query, $user = null)
	{
		$search_condition = Utilities :: query_to_condition($query);;
		$all_objects_searchable = PlatformSetting :: get('all_objects_searchable', RepositoryManager :: APPLICATION_NAME);
		
		if($all_objects_searchable == 1 || is_null($user) || $user->is_platform_admin())
		{
			return $search_condition;
		}
		
    	$conditions = array();
		$conditions[] = $search_condition;
		
		$objects_conditions = array();
		$objects_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user->get_id());
		$objects_conditions[] = $this->get_view_other_objects_condition($user);
		$conditions[] = new OrCondition($objects_conditions);
		
    	return new AndCondition($conditions);
	}
	
	private function get_view_other_objects_condition($user)
    {
    	$conditions = array();
    	
    	$conditions[] = new EqualityCondition(ContentObjectUserShare :: PROPERTY_USER_ID, $user->get_id(), ContentObjectUserShare :: get_table_name());
		
		$group_ids = array();
    	$groups = $user->get_groups();
    	if($groups)
    	{
    		while($group = $groups->next_result())
    		{
    			$group_ids[] = $group->get_id();
    		}
    	
			$conditions[] = new InCondition(ContentObjectGroupShare :: PROPERTY_GROUP_ID, $group_ids, ContentObjectGroupShare :: get_table_name());
    	}
		
		return new OrCondition($conditions);
    }

}
?>