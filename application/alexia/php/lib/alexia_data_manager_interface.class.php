<?php
/**
 * $Id: alexia_data_manager.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia
 */
interface AlexiaDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_alexia_publication($alexia_publication);

    function update_alexia_publication($alexia_publication);

    function delete_alexia_publication($alexia_publication);

    function count_alexia_publications($conditions = null);

    function retrieve_alexia_publication($id);

    function retrieve_alexia_publications($condition = null, $offset = null, $count = null, $order_property = array());

    function create_alexia_publication_group($alexia_publication_group);

    function delete_alexia_publication_group($alexia_publication_group);

    function count_alexia_publication_groups($conditions = null);

    function retrieve_alexia_publication_groups($condition = null, $offset = null, $count = null, $order_property = null);

    function create_alexia_publication_user($alexia_publication_user);

    function delete_alexia_publication_user($alexia_publication_user);

    function count_alexia_publication_users($conditions = null);

    function retrieve_alexia_publication_users($condition = null, $offset = null, $count = null, $order_property = null);

}
?>