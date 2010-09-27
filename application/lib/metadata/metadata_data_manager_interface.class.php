<?php
Interface MetadataDataManagerInterface
{
    function create_storage_unit($name, $properties, $indexes);

    function get_next_metadata_attribute_nesting_id();

    function create_metadata_attribute_nesting($metadata_attribute_nesting);

    function update_metadata_attribute_nesting($metadata_attribute_nesting);

    function delete_metadata_attribute_nestings($table_name, $metadata_property_type);

    function count_metadata_attribute_nestings($condition = null);

    function retrieve_metadata_attribute_nesting($id);

    function retrieve_metadata_attribute_nestings($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function get_next_metadata_namespace_id();

    function create_metadata_namespace($metadata_namespace);

    function update_metadata_namespace($metadata_namespace);

    function delete_metadata_namespace($metadata_namespace);

    function count_metadata_namespaces($condition = null);

    function retrieve_metadata_namespace($id);

    function retrieve_metadata_namespaces($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function get_next_content_object_property_metadata_id();

    function create_content_object_property_metadata($content_object_property_metadata);

    function update_content_object_property_metadata($content_object_property_metadata);

    function delete_content_object_property_metadata($content_object_property_metadata);

    function count_content_object_property_metadatas($condition = null);

    function retrieve_content_object_property_metadata($id);

    function retrieve_content_object_property_metadatas($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function get_next_metadata_property_type_id();

    function create_metadata_property_type($metadata_property_type);

    function update_metadata_property_type($metadata_property_type);

    function delete_metadata_property_type($metadata_property_type);

    function count_metadata_property_types($condition = null);

    function retrieve_metadata_property_type($id);

    function retrieve_metadata_property_types($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function get_next_metadata_property_value_id();

    function create_metadata_property_value($metadata_property_value);

    function update_metadata_property_value($metadata_property_value);

    function delete_metadata_property_value($metadata_property_value);

    function count_metadata_property_values($condition = null);

    function retrieve_metadata_property_value($id);

    function retrieve_metadata_property_values($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function get_next_metadata_property_attribute_type_id();

    function create_metadata_property_attribute_type($metadata_property_attribute_type);

    function update_metadata_property_attribute_type($metadata_property_attribute_type);

    function delete_metadata_property_attribute_type($metadata_property_attribute_type);

    function count_metadata_property_attribute_types($condition = null);

    function retrieve_metadata_property_attribute_type($id);

    function retrieve_metadata_property_attribute_types($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function get_next_metadata_property_attribute_value_id();

    function create_metadata_property_attribute_value($metadata_property_attribute_value);

    function update_metadata_property_attribute_value($metadata_property_attribute_value);

    function delete_metadata_property_attribute_value($metadata_property_attribute_value);

    function count_metadata_property_attribute_values($condition = null);

    function retrieve_metadata_property_attribute_value($id);

    function retrieve_metadata_property_attribute_values($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function get_next_metadata_property_nesting_id();

    function create_metadata_property_nesting($metadata_property_nesting);

    function update_metadata_property_nesting($metadata_property_nesting);

    function delete_metadata_property_nestings($table_name, $metadata_property_nesting);

    function count_metadata_property_nestings($condition = null);

    function retrieve_metadata_property_nesting($id);

    function retrieve_metadata_property_nestings($condition = null, $offset = null, $max_objects = null, $order_by = null);
}
?>
