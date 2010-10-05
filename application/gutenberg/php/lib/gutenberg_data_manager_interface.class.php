<?php
/**
 * $Id: gutenberg_data_manager.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenberg
 */
interface GutenbergDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_gutenberg_publication($gutenberg_publication);

    function update_gutenberg_publication($gutenberg_publication);

    function delete_gutenberg_publication($gutenberg_publication);

    function count_gutenberg_publications($conditions = null);

    function retrieve_gutenberg_publication($id);

    function retrieve_gutenberg_publications($condition = null, $offset = null, $count = null, $order_property = array());

}
?>