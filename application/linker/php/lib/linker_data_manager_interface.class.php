<?php
namespace application\linker;
interface LinkerDataManagerInterface
{
    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function delete_linker($link);

    function update_linker($link);

    function create_linker($link);

    function retrieve_link($id);

    function count_links($conditions = null);

    function retrieve_links($condition = null, $offset = null, $count = null, $order_property = null);
}
?>