<?php
/**
 * @package metadata.datamanager
 */
require_once dirname(__FILE__).'/../metadata_attribute_nesting.class.php';
require_once dirname(__FILE__).'/../metadata_namespace.class.php';
require_once dirname(__FILE__).'/../content_object_property_metadata.class.php';
require_once dirname(__FILE__).'/../metadata_property_nesting.class.php';
require_once dirname(__FILE__).'/../metadata_property_type.class.php';
require_once dirname(__FILE__).'/../metadata_property_value.class.php';
require_once dirname(__FILE__).'/../metadata_property_attribute_type.class.php';
require_once dirname(__FILE__).'/../metadata_property_attribute_value.class.php';
require_once dirname(__FILE__).'/../metadata_data_manager_interface.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author Jens Vanderheyden
 */

class DatabaseMetadataDataManager extends Database implements MetadataDataManagerInterface
{
	private $database;

	function initialize()
	{
		$aliases = array();
		$aliases[MetadataAttributeNesting :: get_table_name()] = 'meng';
		$aliases[MetadataNamespace :: get_table_name()] = 'mece';
		$aliases[ContentObjectPropertyMetadata :: get_table_name()] = 'cota';
		$aliases[MetadataPropertyType :: get_table_name()] = 'mepe';
		$aliases[MetadataPropertyValue :: get_table_name()] = 'meue';
		$aliases[MetadataPropertyAttributeType :: get_table_name()] = 'mepe';
		$aliases[MetadataPropertyAttributeValue :: get_table_name()] = 'meue';

		$this->database = new Database($aliases);
		$this->database->set_prefix('metadata_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_metadata_attribute_nesting_id()
	{
		return $this->database->get_next_id(MetadataAttributeNesting :: get_table_name());
	}

	function create_metadata_attribute_nesting($metadata_attribute_nesting)
	{
		return $this->database->create($metadata_attribute_nesting);
	}

	function update_metadata_attribute_nesting($metadata_attribute_nesting)
	{
		$condition = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_ID, $metadata_attribute_nesting->get_id());
		return $this->database->update($metadata_attribute_nesting, $condition);
	}

	function delete_metadata_attribute_nestings($table_name, $metadata_property_type)
	{
		$condition = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $metadata_property_type->get_id());
		return $this->database->delete($table_name, $condition);
	}

	function count_metadata_attribute_nestings($condition = null)
	{
		return $this->database->count_objects(MetadataAttributeNesting :: get_table_name(), $condition);
	}

	function retrieve_metadata_attribute_nesting($id)
	{
		$condition = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(MetadataAttributeNesting :: get_table_name(), $condition);
	}

	function retrieve_metadata_attribute_nestings($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(MetadataAttributeNesting :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_metadata_namespace_id()
	{
		return $this->database->get_next_id(MetadataNamespace :: get_table_name());
	}

	function create_metadata_namespace($metadata_namespace)
	{
		return $this->database->create($metadata_namespace);
	}

	function update_metadata_namespace($metadata_namespace)
	{
		$condition = new EqualityCondition(MetadataNamespace :: PROPERTY_NS_PREFIX, $metadata_namespace->get_ns_prefix());
		return $this->database->update($metadata_namespace, $condition);
	}

	function delete_metadata_namespace($metadata_namespace)
	{
		$condition = new EqualityCondition(MetadataNamespace :: PROPERTY_NS_PREFIX, $metadata_namespace->get_ns_prefix());
		return $this->database->delete($metadata_namespace->get_table_name(), $condition);
	}

	function count_metadata_namespaces($condition = null)
	{
		return $this->database->count_objects(MetadataNamespace :: get_table_name(), $condition);
	}

	function retrieve_metadata_namespace($ns_prefix)
	{
		$condition = new EqualityCondition(MetadataNamespace :: PROPERTY_NS_PREFIX, $ns_prefix);
		return $this->database->retrieve_object(MetadataNamespace :: get_table_name(), $condition);
	}

	function retrieve_metadata_namespaces($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(MetadataNamespace :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_metadata_property_nesting_id()
	{
		return $this->database->get_next_id(MetadataPropertyNesting :: get_table_name());
	}

	function create_metadata_property_nesting($metadata_property_nesting)
	{
		return $this->database->create($metadata_property_nesting);
	}

	function update_metadata_property_nesting($metadata_property_nesting)
	{
		$condition = new EqualityCondition(MetadataPropertyNesting :: PROPERTY_ID, $metadata_property_nesting->get_id());
		return $this->database->update($metadata_property_nesting, $condition);
	}

	function delete_metadata_property_nestings($table_name, $metadata_property_type)
	{
		$condition = new EqualityCondition(MetadataPropertyNesting :: PROPERTY_PARENT_ID, $metadata_property_type->get_id());
		return $this->database->delete($table_name, $condition);
	}

	function count_metadata_property_nestings($condition = null)
	{
		return $this->database->count_objects(MetadataPropertyNesting :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_nesting($id)
	{
		$condition = new EqualityCondition(MetadataPropertyNesting :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(MetadataPropertyNesting :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_nestings($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(MetadataPropertyNesting :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

        function get_next_content_object_property_metadata_id()
	{
		return $this->database->get_next_id(ContentObjectPropertyMetadata :: get_table_name());
	}

	function create_content_object_property_metadata($content_object_property_metadata)
	{
		return $this->database->create($content_object_property_metadata);
	}

	function update_content_object_property_metadata($content_object_property_metadata)
	{
		$condition = new EqualityCondition(ContentObjectPropertyMetadata :: PROPERTY_ID, $content_object_property_metadata->get_id());
		return $this->database->update($content_object_property_metadata, $condition);
	}

	function delete_content_object_property_metadata($content_object_property_metadata)
	{
		$condition = new EqualityCondition(ContentObjectPropertyMetadata :: PROPERTY_ID, $content_object_property_metadata->get_id());
		return $this->database->delete($content_object_property_metadata->get_table_name(), $condition);
	}

	function count_content_object_property_metadatas($condition = null)
	{
		return $this->database->count_objects(ContentObjectPropertyMetadata :: get_table_name(), $condition);
	}

	function retrieve_content_object_property_metadata($id)
	{
		$condition = new EqualityCondition(ContentObjectPropertyMetadata :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(ContentObjectPropertyMetadata :: get_table_name(), $condition);
	}

	function retrieve_content_object_property_metadatas($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(ContentObjectPropertyMetadata :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_metadata_property_type_id()
	{
		return $this->database->get_next_id(MetadataPropertyType :: get_table_name());
	}

	function create_metadata_property_type($metadata_property_type)
	{
		return $this->database->create($metadata_property_type);
	}

	function update_metadata_property_type($metadata_property_type)
	{
            $condition = new EqualityCondition(MetadataPropertyType :: PROPERTY_ID, $metadata_property_type->get_id());
            return $this->database->update($metadata_property_type, $condition);
	}

	function delete_metadata_property_type($metadata_property_type)
	{
		$condition = new EqualityCondition(MetadataPropertyType :: PROPERTY_ID, $metadata_property_type->get_id());
		return $this->database->delete($metadata_property_type->get_table_name(), $condition);
	}

	function count_metadata_property_types($condition = null)
	{
		return $this->database->count_objects(MetadataPropertyType :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_type($id)
	{
		$condition = new EqualityCondition(MetadataPropertyType :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(MetadataPropertyType :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_types($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(MetadataPropertyType :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_metadata_property_value_id()
	{
		return $this->database->get_next_id(MetadataPropertyValue :: get_table_name());
	}

	function create_metadata_property_value($metadata_property_value)
	{
		return $this->database->create($metadata_property_value);
	}

	function update_metadata_property_value($metadata_property_value)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $metadata_property_value->get_id());
		return $this->database->update($metadata_property_value, $condition);
	}

	function delete_metadata_property_value($metadata_property_value)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $metadata_property_value->get_id());
		return $this->database->delete($metadata_property_value->get_table_name(), $condition);
	}

	function count_metadata_property_values($condition = null)
	{
		return $this->database->count_objects(MetadataPropertyValue :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_value($id)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(MetadataPropertyValue :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(MetadataPropertyValue :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_metadata_property_attribute_type_id()
	{
		return $this->database->get_next_id(MetadataPropertyAttributeType :: get_table_name());
	}

	function create_metadata_property_attribute_type($metadata_property_attribute_type)
	{
		return $this->database->create($metadata_property_attribute_type);
	}

	function update_metadata_property_attribute_type($metadata_property_attribute_type)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeType :: PROPERTY_ID, $metadata_property_attribute_type->get_id());
		return $this->database->update($metadata_property_attribute_type, $condition);
	}

	function delete_metadata_property_attribute_type($metadata_property_attribute_type)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeType :: PROPERTY_ID, $metadata_property_attribute_type->get_id());
		return $this->database->delete($metadata_property_attribute_type->get_table_name(), $condition);
	}

	function count_metadata_property_attribute_types($condition = null)
	{
		return $this->database->count_objects(MetadataPropertyAttributeType :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_attribute_type($id)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeType :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(MetadataPropertyAttributeType :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_attribute_types($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(MetadataPropertyAttributeType :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_metadata_property_attribute_value_id()
	{
		return $this->database->get_next_id(MetadataPropertyAttributeValue :: get_table_name());
	}

	function create_metadata_property_attribute_value($metadata_property_attribute_value)
	{
		return $this->database->create($metadata_property_attribute_value);
	}

	function update_metadata_property_attribute_value($metadata_property_attribute_value)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_ID, $metadata_property_attribute_value->get_id());
		return $this->database->update($metadata_property_attribute_value, $condition);
	}

	function delete_metadata_property_attribute_value($metadata_property_attribute_value)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_ID, $metadata_property_attribute_value->get_id());
		return $this->database->delete($metadata_property_attribute_value->get_table_name(), $condition);
	}

	function count_metadata_property_attribute_values($condition = null)
	{
		return $this->database->count_objects(MetadataPropertyAttributeValue :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_attribute_value($id)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(MetadataPropertyAttributeValue :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_attribute_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(MetadataPropertyAttributeValue :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

}
?>