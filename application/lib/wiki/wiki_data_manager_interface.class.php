<?php
interface WikiDataManagerInterface
{
    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_wiki_publication($wiki_publication);

    function update_wiki_publication($wiki_publication);

    function delete_wiki_publication($wiki_publication);

    function count_wiki_publications($conditions = null);

    function retrieve_wiki_publication($id);

    function retrieve_wiki_publications($condition = null, $offset = null, $count = null, $order_property = null);
}
?>