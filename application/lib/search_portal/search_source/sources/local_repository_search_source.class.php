<?php
/**
 * $Id: local_repository_search_source.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_source
 */
require_once dirname(__FILE__) . '/../search_source.class.php';

class LocalRepositorySearchSource extends SearchSource
{
    function retrieve_search_results($query, $offset = 0, $max_objects = -1)
    {
    	 $condition = Utilities :: query_to_condition($query);
    	 return RepositoryDataManager :: get_instance()->retrieve_content_objects($condition, array(new ObjectTableOrder(ContentObject :: PROPERTY_TITLE)), $offset, $max_objects)->as_array();
    }
    
	function count_search_results($query)
	{
		$condition = Utilities :: query_to_condition($query);
		return RepositoryDataManager :: get_instance()->count_content_objects($condition);
	}

}
?>