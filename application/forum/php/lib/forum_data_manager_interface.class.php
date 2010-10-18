<?php
interface ForumDataManagerInterface
{
    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_forum_publication($forum_publication);

    function update_forum_publication($forum_publication);

    function delete_forum_publication($forum_publication);

    function count_forum_publications($conditions = null);

    function retrieve_forum_publication($id);

    function retrieve_forum_publications($condition = null, $offset = null, $count = null, $order_property = null);

    function move_forum_publication($publication, $places);

    function create_forum_publication_category($forum_publication);

    function update_forum_publication_category($forum_publication);

    function delete_forum_publication_category($forum_publication);

    function count_forum_publication_categories($conditions = null);

    function retrieve_forum_publication_categories($condition = null, $offset = null, $count = null, $order_property = null);

}
?>