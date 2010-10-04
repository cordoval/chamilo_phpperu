<?php

/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class ContentObjectUserShareRightsBrowserTableDataProvider extends ObjectTableDataProvider
{
    /**
     * returns the users that u share with
     * @param <type> $offset
     * @param <type> $count
     * @param <type> $order_property
     * @return <type> ObjectResultSet
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $repo_data_manager = RepositoryDataManager :: get_instance();
        return $repo_data_manager->retrieve_content_object_user_shares();
    }

    function get_object_count()
    {
        return RepositoryDataManager :: get_instance()->count_content_object_user_shares();
    }

}

?>