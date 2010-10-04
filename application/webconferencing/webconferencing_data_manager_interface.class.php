<?php
interface WebconferencingDataManagerInterface
{
    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_webconference($webconference);

    function update_webconference($webconference);

    function delete_webconference($webconference);

    function count_webconferences($conditions = null);

    function retrieve_webconference($id);

    function retrieve_webconferences($condition = null, $offset = null, $count = null, $order_property = null);

    function create_webconference_option($webconference_option);

    function update_webconference_option($webconference_option);

    function delete_webconference_options($webconference);

    function delete_webconference_option($webconference_option);

    function count_webconference_options($conditions = null);

    function retrieve_webconference_option($id);

    function retrieve_webconference_options($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_webconference_groups($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function retrieve_webconference_users($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function create_webconference_user($webconference_user);

    function create_webconference_group($webconference_group);
}
?>