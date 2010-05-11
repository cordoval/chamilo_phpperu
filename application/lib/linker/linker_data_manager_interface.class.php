<?php
interface LinkerDataManagerInterface
{
    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function delete_link($link);

    function update_link($link);

    function create_link($link);

    function retrieve_link($id);

    function count_links($conditions = null);

    function retrieve_links($condition = null, $offset = null, $count = null, $order_property = null);
}
?>