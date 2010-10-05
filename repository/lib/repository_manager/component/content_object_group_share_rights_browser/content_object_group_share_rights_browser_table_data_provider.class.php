<?php

/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class ContentObjectGroupShareRightsBrowserTableDataProvider extends ObjectTableDataProvider
{
    /**
     * returns the groups that u share with
     * @param <type> $offset
     * @param <type> $count
     * @param <type> $order_property
     * @return <type> ObjectResultSet
     */
    function get_objects($offset, $count, $order_property = null)
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_object_group_shares($this->get_condition());
    }

    function get_object_count()
    {
        $conditon = new EqualityCondition(ContentObjectGroupShare :: PROPERTY_CONTENT_OBJECT_ID, Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID));
        return RepositoryDataManager :: get_instance()->count_content_object_group_shares($conditon);
    }

}

?>