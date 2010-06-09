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
	  	return RepositoryDataManager :: get_instance()->retrieve_content_objects($this->get_condition($query, $user), array(new ObjectTableOrder(ContentObject :: PROPERTY_TITLE)), $offset, $max_objects, $this->get_query())->as_array();
    }
    
	function count_search_results($query, $user = null)
	{
		return RepositoryDataManager :: get_instance()->count_content_objects($this->get_condition($query, $user), $this->get_query(true));
	}
	
	private function get_query($count = false)
	{
		$data_manager = RepositoryDataManager :: get_instance();
    	$rights_data_manager = RightsDataManager :: get_instance();
    	
        $content_object_alias = $data_manager->get_alias(ContentObject :: get_table_name());
    	$location_alias = $data_manager->get_alias(Location :: get_table_name());
    	$user_alias = $data_manager->get_alias(UserRightLocation :: get_table_name());
    	$group_alias = $data_manager->get_alias(GroupRightLocation :: get_table_name());

        $query = 'SELECT ' . $content_object_alias . '.* FROM ';
        if($count)
        	$query = 'SELECT COUNT(*) FROM ';
        $query .= $data_manager->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $content_object_alias;
       	$query .= ' JOIN ' . $data_manager->escape_table_name('content_object_version') . ' AS ' . DatabaseRepositoryDataManager :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . ' ON ' . $content_object_alias . '.' . ContentObject :: PROPERTY_ID . ' = ' . DatabaseRepositoryDataManager :: ALIAS_CONTENT_OBJECT_VERSION_TABLE . '.' . ContentObject :: PROPERTY_ID;
       	$query .= ' LEFT JOIN ' . $rights_data_manager->escape_table_name(Location :: get_table_name()) . ' AS ' . $location_alias . ' ON ' . $content_object_alias . '.' . ContentObject :: PROPERTY_ID . ' = ' .  $location_alias . '.' . Location :: PROPERTY_IDENTIFIER;
        $query .= ' LEFT JOIN ' . $rights_data_manager->escape_table_name(UserRightLocation :: get_table_name()) . ' AS ' . $user_alias . ' ON ' . $user_alias . '.' . UserRightLocation :: PROPERTY_LOCATION_ID . ' = ' .  $location_alias . '.' . Location :: PROPERTY_ID;
       	$query .= ' LEFT JOIN ' . $rights_data_manager->escape_table_name(GroupRightLocation :: get_table_name()) . ' AS ' . $group_alias. ' ON ' . $group_alias . '.' . GroupRightLocation :: PROPERTY_LOCATION_ID . ' = ' .  $location_alias . '.' . Location :: PROPERTY_ID;
       	
       	return $query;
	}
	
	private function get_condition($query, $user = null)
	{
		//TODO check for admin
		$set_rights = !is_null($user);
    	
    	if($set_rights)
    	{
    		$right_conditions = array();
    		$right_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user->get_id());
    		$right_conditions[] = new AndCondition(	new EqualityCondition(UserRightLocation :: PROPERTY_USER_ID, $user->get_id(), UserRightLocation :: get_table_name()),
    												new EqualityCondition(UserRightLocation :: PROPERTY_RIGHT_ID, RepositoryRights :: SEARCH_RIGHT, UserRightLocation :: get_table_name()));
    		$right_conditions[] = new AndCondition(	new InCondition(GroupRightLocation :: PROPERTY_GROUP_ID, $user->get_groups(true), GroupRightLocation :: get_table_name()),
    												new EqualityCondition(GroupRightLocation :: PROPERTY_RIGHT_ID, RepositoryRights :: SEARCH_RIGHT, GroupRightLocation :: get_table_name()));
    	}
    	
    	if($set_rights)
    		$condition = new AndCondition(Utilities :: query_to_condition($query), new OrCondition($right_conditions));
    	else
    		$condition = Utilities :: query_to_condition($query);
    		
    	return $condition;
	}

}
?>