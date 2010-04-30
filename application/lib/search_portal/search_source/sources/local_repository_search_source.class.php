<?php
/**
 * $Id: local_repository_search_source.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_source
 */
require_once dirname(__FILE__) . '/../search_source.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once dirname(__FILE__) . '/../repository_search_result.class.php';

class LocalRepositorySearchSource extends SearchSource
{
    function search($query)
    {
        $condition = Utilities :: query_to_condition($query);

        $adm = AdminDataManager :: get_instance();
        $repository_title = PlatformSetting :: get('site_name');

        $repository_url = Path :: get(WEB_PATH);
        $returned_results = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition, array(new ObjectTableOrder(ContentObject :: PROPERTY_TITLE)));
        $result_count = count($returned_results);
        return new RepositorySearchResult($repository_title, $repository_url, $returned_results, $result_count);
    }
}
?>