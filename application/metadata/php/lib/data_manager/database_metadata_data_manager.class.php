<?php
namespace application\metadata;
use common\libraries\Database;
use common\libraries\EqualityCondition;
use common\libraries\ConditionTranslator;

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author Jens Vanderheyden
 */

class DatabaseMetadataDataManager extends Database implements MetadataDataManagerInterface
{
	function initialize()
	{
            parent :: initialize();
            $this->set_prefix('metadata_');
	}

        function get_next_metadata_attribute_nesting_id()
	{
		return $this->get_next_id(MetadataAttributeNesting :: get_table_name());
	}

	function create_metadata_attribute_nesting($metadata_attribute_nesting)
	{
		return $this->create($metadata_attribute_nesting);
	}

	function update_metadata_attribute_nesting($metadata_attribute_nesting)
	{
		$condition = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_ID, $metadata_attribute_nesting->get_id());
		return $this->update($metadata_attribute_nesting, $condition);
	}

	function delete_metadata_attribute_nestings($table_name, $metadata_property_type)
	{
		$condition = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $metadata_property_type->get_id());
		return $this->delete($table_name, $condition);
	}

	function count_metadata_attribute_nestings($condition = null)
	{
		return $this->count_objects(MetadataAttributeNesting :: get_table_name(), $condition);
	}

	function retrieve_metadata_attribute_nesting($id)
	{
		$condition = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_ID, $id);
		return $this->retrieve_object(MetadataAttributeNesting :: get_table_name(), $condition, MetadataAttributeNesting :: CLASS_NAME);
	}

	function retrieve_metadata_attribute_nestings($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(MetadataAttributeNesting :: get_table_name(), $condition, $offset, $max_objects, $order_by, MetadataAttributeNesting :: CLASS_NAME);
	}

	function get_next_metadata_namespace_id()
	{
		return $this->get_next_id(MetadataNamespace :: get_table_name());
	}

	function create_metadata_namespace($metadata_namespace)
	{
		return $this->create($metadata_namespace);
	}

	function update_metadata_namespace($metadata_namespace)
	{
		$condition = new EqualityCondition(MetadataNamespace :: PROPERTY_ID, $metadata_namespace->get_id());
		return $this->update($metadata_namespace, $condition);
	}

	function delete_metadata_namespace($metadata_namespace)
	{
		$condition = new EqualityCondition(MetadataNamespace :: PROPERTY_ID, $metadata_namespace->get_id());
		return $this->delete($metadata_namespace->get_table_name(), $condition);
	}

	function count_metadata_namespaces($condition = null)
	{
		return $this->count_objects(MetadataNamespace :: get_table_name(), $condition);
	}

	function retrieve_metadata_namespace($id)
	{
            $condition = new EqualityCondition(MetadataNamespace :: PROPERTY_ID, $id);
            return $this->retrieve_object(MetadataNamespace :: get_table_name(), $condition, null, MetadataNamespace :: CLASS_NAME);
	}

	function retrieve_metadata_namespaces($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(MetadataNamespace :: get_table_name(), $condition, $offset, $max_objects, $order_by, MetadataNamespace :: CLASS_NAME);
	}

	function get_next_metadata_property_nesting_id()
	{
		return $this->get_next_id(MetadataPropertyNesting :: get_table_name());
	}

	function create_metadata_property_nesting($metadata_property_nesting)
	{
		return $this->create($metadata_property_nesting);
	}

	function update_metadata_property_nesting($metadata_property_nesting)
	{
		$condition = new EqualityCondition(MetadataPropertyNesting :: PROPERTY_ID, $metadata_property_nesting->get_id());
		return $this->update($metadata_property_nesting, $condition);
	}

	function delete_metadata_property_nestings($table_name, $metadata_property_type)
	{
		$condition = new EqualityCondition(MetadataPropertyNesting :: PROPERTY_PARENT_ID, $metadata_property_type->get_id());
		return $this->delete($table_name, $condition);
	}

	function count_metadata_property_nestings($condition = null)
	{
		return $this->count_objects(MetadataPropertyNesting :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_nesting($id)
	{
		$condition = new EqualityCondition(MetadataPropertyNesting :: PROPERTY_ID, $id);
		return $this->retrieve_object(MetadataPropertyNesting :: get_table_name(), $condition,  null, MetadataPropertyNesting :: CLASS_NAME);
	}

	function retrieve_metadata_property_nestings($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(MetadataPropertyNesting :: get_table_name(), $condition, $offset, $max_objects, $order_by, MetadataPropertyNesting :: CLASS_NAME);
	}

        function get_next_content_object_property_metadata_id()
	{
		return $this->get_next_id(ContentObjectPropertyMetadata :: get_table_name());
	}

	function create_content_object_property_metadata($content_object_property_metadata)
	{
		return $this->create($content_object_property_metadata);
	}

	function update_content_object_property_metadata($content_object_property_metadata)
	{
		$condition = new EqualityCondition(ContentObjectPropertyMetadata :: PROPERTY_ID, $content_object_property_metadata->get_id());
		return $this->update($content_object_property_metadata, $condition);
	}

	function delete_content_object_property_metadata($content_object_property_metadata)
	{
		$condition = new EqualityCondition(ContentObjectPropertyMetadata :: PROPERTY_ID, $content_object_property_metadata->get_id());
		return $this->delete($content_object_property_metadata->get_table_name(), $condition);
	}

	function count_content_object_property_metadatas($condition = null)
	{
		return $this->count_objects(ContentObjectPropertyMetadata :: get_table_name(), $condition);
	}

	function retrieve_content_object_property_metadata($id)
	{
		$condition = new EqualityCondition(ContentObjectPropertyMetadata :: PROPERTY_ID, $id);
		return $this->retrieve_object(ContentObjectPropertyMetadata :: get_table_name(), $condition,  null, ContentObjectPropertyMetadata :: CLASS_NAME);
	}

	function retrieve_content_object_property_metadatas($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(ContentObjectPropertyMetadata :: get_table_name(), $condition, $offset, $max_objects, $order_by, ContentObjectPropertyMetadata :: CLASS_NAME);
	}

	function get_next_metadata_property_type_id()
	{
		return $this->get_next_id(MetadataPropertyType :: get_table_name());
	}

	function create_metadata_property_type($metadata_property_type)
	{
		return $this->create($metadata_property_type);
	}

	function update_metadata_property_type($metadata_property_type)
	{
            $condition = new EqualityCondition(MetadataPropertyType :: PROPERTY_ID, $metadata_property_type->get_id());
            return $this->update($metadata_property_type, $condition);
	}

	function delete_metadata_property_type($metadata_property_type)
	{
		$condition = new EqualityCondition(MetadataPropertyType :: PROPERTY_ID, $metadata_property_type->get_id());
		return $this->delete($metadata_property_type->get_table_name(), $condition);
	}

	function count_metadata_property_types($condition = null)
	{
		return $this->count_objects(MetadataPropertyType :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_type($id)
	{
		$condition = new EqualityCondition(MetadataPropertyType :: PROPERTY_ID, $id);
		return $this->retrieve_object(MetadataPropertyType :: get_table_name(), $condition,  null, MetadataPropertyType :: CLASS_NAME);
	}

	function retrieve_metadata_property_types($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(MetadataPropertyType :: get_table_name(), $condition, $offset, $max_objects, $order_by, MetadataPropertyType :: CLASS_NAME);
	}

	function get_next_content_object_metadata_property_value_id()
	{
		return $this->get_next_id(ContentObjectMetadataPropertyValue :: get_table_name());
	}

	function create_content_object_metadata_property_value($content_object_metadata_property_value)
	{
		return $this->create($content_object_metadata_property_value);
	}

	function update_content_object_metadata_property_value($content_object_metadata_property_value)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $content_object_metadata_property_value->get_id());
		return $this->update($content_object_metadata_property_value, $condition);
	}

	function delete_content_object_metadata_property_value($content_object_metadata_property_value)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $content_object_metadata_property_value->get_id());
		return $this->delete($content_object_metadata_property_value->get_table_name(), $condition);
	}

	function count_content_object_metadata_property_values($condition = null)
	{
		return $this->count_objects(ContentObjectMetadataPropertyValue :: get_table_name(), $condition);
	}

	function retrieve_content_object_metadata_property_value($id)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $id);
		return $this->retrieve_object(ContentObjectMetadataPropertyValue :: get_table_name(), $condition, null, ContentObjectMetadataPropertyValue :: CLASS_NAME);
	}

	function retrieve_content_object_metadata_property_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(ContentObjectMetadataPropertyValue :: get_table_name(), $condition, $offset, $max_objects, $order_by, ContentObjectMetadataPropertyValue :: CLASS_NAME);
	}

        /*
         * retrieves metadata property values from content object preformatted
         * @return array property values [$n] = prefix : property type => prefix property value
         */
        function retrieve_full_content_object_metadata_property_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
        {
            $namespace_alias = $this->get_alias(MetadataNamespace :: get_table_name());
            $type_alias = $this->get_alias(MetadataPropertyType :: get_table_name());
            $value_alias = $this->get_alias(ContentObjectMetadataPropertyValue :: get_table_name());

            $query = 'SELECT ' . $value_alias . '.' . MetadataPropertyValue :: PROPERTY_ID . ', ' . $value_alias . '.' . MetadataPropertyValue :: PROPERTY_VALUE . ', ' .$namespace_alias . '.' . MetadataNamespace :: PROPERTY_NS_PREFIX . ', ' . $type_alias . '.' . MetadataPropertyType :: PROPERTY_NAME;
            $query .= ' FROM ' . $this->escape_table_name(ContentObjectMetadataPropertyValue :: get_table_name()) . ' AS ' . $value_alias;
            $query .= ' LEFT JOIN ' . $this->escape_table_name(MetadataPropertyType :: get_table_name()) . ' AS ' . $type_alias;
            $query .= ' ON ' . $this->escape_column_name(MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, $value_alias) . ' = '. $this->escape_column_name(MetadataPropertyType :: PROPERTY_ID, $type_alias);
            $query .= ' LEFT JOIN ' . $this->escape_table_name(MetadataNamespace :: get_table_name()) . ' AS ' . $namespace_alias;
            $query .= ' ON ' . $this->escape_column_name(MetadataNamespace :: PROPERTY_ID, $namespace_alias) . ' = '. $this->escape_column_name(MetadataPropertyType :: PROPERTY_NAMESPACE, $type_alias);

            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);

            $res = $this->query($query);

            $property_values = array();
            while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
                $property_values[$record[MetadataPropertyValue :: PROPERTY_ID]] = $record[MetadataPropertyType :: PROPERTY_NS_PREFIX] . ':' . $record[MetadataPropertyType :: PROPERTY_NAME] . '=' .$record[MetadataPropertyValue :: PROPERTY_VALUE];
            }
            $res->free();
            return $property_values;
        }

        /*
         * retrieves metadata properties and values
         * @return array property values [prefix : property type] => prefix property value
         */
        function retrieve_content_object_metadata_properties_and_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
        {
            $namespace_alias = $this->get_alias(MetadataNamespace :: get_table_name());
            $type_alias = $this->get_alias(MetadataPropertyType :: get_table_name());
            $value_alias = $this->get_alias(ContentObjectMetadataPropertyValue :: get_table_name());

            $query = 'SELECT ' . $value_alias . '.' . MetadataPropertyValue :: PROPERTY_ID . ', ' . $value_alias . '.' . MetadataPropertyValue :: PROPERTY_VALUE . ', ' .$namespace_alias . '.' . MetadataNamespace :: PROPERTY_NS_PREFIX . ', ' . $type_alias . '.' . MetadataPropertyType :: PROPERTY_NAME;
            $query .= ' FROM ' . $this->escape_table_name(ContentObjectMetadataPropertyValue :: get_table_name()) . ' AS ' . $value_alias;
            $query .= ' LEFT JOIN ' . $this->escape_table_name(MetadataPropertyType :: get_table_name()) . ' AS ' . $type_alias;
            $query .= ' ON ' . $this->escape_column_name(MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, $value_alias) . ' = '. $this->escape_column_name(MetadataPropertyType :: PROPERTY_ID, $type_alias);
            $query .= ' LEFT JOIN ' . $this->escape_table_name(MetadataNamespace :: get_table_name()) . ' AS ' . $namespace_alias;
            $query .= ' ON ' . $this->escape_column_name(MetadataNamespace :: PROPERTY_ID, $namespace_alias) . ' = '. $this->escape_column_name(MetadataPropertyType :: PROPERTY_NAMESPACE, $type_alias);

            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);

            $res = $this->query($query);

            $property_values = array();
            while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
                $property_values[$record[MetadataPropertyType :: PROPERTY_NS_PREFIX] . ':' . $record[MetadataPropertyType :: PROPERTY_NAME]] = $record[MetadataPropertyValue :: PROPERTY_VALUE];
            }
            $res->free();
            return $property_values;
        }
        


        function get_next_user_metadata_property_value_id()
	{
		return $this->get_next_id(UserMetadataPropertyValue :: get_table_name());
	}

	function create_user_metadata_property_value($user_metadata_property_value)
	{
		return $this->create($user_metadata_property_value);
	}

	function update_user_metadata_property_value($user_metadata_property_value)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $user_metadata_property_value->get_id());
		return $this->update($user_metadata_property_value, $condition);
	}

	function delete_user_metadata_property_value($user_metadata_property_value)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $user_metadata_property_value->get_id());
		return $this->delete($user_metadata_property_value->get_table_name(), $condition);
	}

	function count_user_metadata_property_values($condition = null)
	{
		return $this->count_objects(UserMetadataPropertyValue :: get_table_name(), $condition);
	}

	function retrieve_user_metadata_property_value($id)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $id);
		return $this->retrieve_object(UserMetadataPropertyValue :: get_table_name(), $condition, null, UserMetadataPropertyValue :: CLASS_NAME);
	}

	function retrieve_user_metadata_property_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(UserMetadataPropertyValue :: get_table_name(), $condition, $offset, $max_objects, $order_by, UserMetadataPropertyValue :: CLASS_NAME);
	}



        function get_next_group_metadata_property_value_id()
	{
		return $this->get_next_id(GroupMetadataPropertyValue :: get_table_name());
	}

	function create_group_metadata_property_value($group_metadata_property_value)
	{
		return $this->create($group_metadata_property_value);
	}

	function update_group_metadata_property_value($group_metadata_property_value)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $group_metadata_property_value->get_id());
		return $this->update($group_metadata_property_value, $condition);
	}

	function delete_group_metadata_property_value($group_metadata_property_value)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $group_metadata_property_value->get_id());
		return $this->delete($group_metadata_property_value->get_table_name(), $condition);
	}

	function count_group_metadata_property_values($condition = null)
	{
		return $this->count_objects(GroupMetadataPropertyValue :: get_table_name(), $condition);
	}

	function retrieve_group_metadata_property_value($id)
	{
		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $id);
		return $this->retrieve_object(GroupMetadataPropertyValue :: get_table_name(), $condition, null, GroupMetadataPropertyValue :: CLASS_NAME);
	}

	function retrieve_group_metadata_property_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(GroupMetadataPropertyValue :: get_table_name(), $condition, $offset, $max_objects, $order_by, GroupMetadataPropertyValue :: CLASS_NAME);
	}


//      function get_next_metadata_property_value_id()
//	{
//		return $this->get_next_id(MetadataPropertyValue :: get_table_name());
//	}
//
//	function create_metadata_property_value($metadata_property_value)
//	{
//		return $this->create($metadata_property_value);
//	}
//
//	function update_metadata_property_value($metadata_property_value)
//	{
//		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $metadata_property_value->get_id());
//		return $this->update($metadata_property_value, $condition);
//	}
//
//	function delete_metadata_property_value($metadata_property_value)
//	{
//		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $metadata_property_value->get_id());
//		return $this->delete($metadata_property_value->get_table_name(), $condition);
//	}
//
//	function count_metadata_property_values($condition = null)
//	{
//		return $this->count_objects(MetadataPropertyValue :: get_table_name(), $condition);
//	}
//
//	function retrieve_metadata_property_value($id)
//	{
//		$condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_ID, $id);
//		return $this->retrieve_object(MetadataPropertyValue :: get_table_name(), $condition, null, MetadataPropertyValue :: CLASS_NAME);
//	}
//
//	function retrieve_metadata_property_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
//	{
//		return $this->retrieve_objects(MetadataPropertyValue :: get_table_name(), $condition, $offset, $max_objects, $order_by, MetadataPropertyValue :: CLASS_NAME);
//	}
//
//        function retrieve_full_metadata_property_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
//        {
//            $type_alias = $this->get_alias(MetadataPropertyType :: get_table_name());
//            $value_alias = $this->get_alias(MetadataPropertyValue :: get_table_name());
//
//            $query = 'SELECT ' . $value_alias . '.' . MetadataPropertyValue :: PROPERTY_ID . ', ' . $value_alias . '.' . MetadataPropertyValue :: PROPERTY_VALUE . ', ' .$type_alias . '.' . MetadataPropertyType :: PROPERTY_NS_PREFIX . ', ' . $type_alias . '.' . MetadataPropertyType :: PROPERTY_NAME;
//            $query .= ' FROM ' . $this->escape_table_name(MetadataPropertyValue :: get_table_name()) . ' AS ' . $value_alias;
//            $query .= ' LEFT JOIN ' . $this->escape_table_name(MetadataPropertyType :: get_table_name()) . ' AS ' . $type_alias;
//            $query .= ' ON ' . $this->escape_column_name(MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID, $value_alias) . ' = '. $this->escape_column_name(MetadataPropertyType :: PROPERTY_ID, $type_alias);
//
//            $translator = new ConditionTranslator($this);
//            $query .= $translator->render_query($condition);
//
//            $res = $this->query($query);
//
//            $property_values = array();
//            while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
//            {
//                $property_values[$record[MetadataPropertyValue :: PROPERTY_ID]] = $record[MetadataPropertyType :: PROPERTY_NS_PREFIX] . ':' . $record[MetadataPropertyType :: PROPERTY_NAME] . '=' .$record[MetadataPropertyValue :: PROPERTY_VALUE];
//            }
//            $res->free();
//            return $property_values;
//        }

	function get_next_metadata_property_attribute_type_id()
	{
		return $this->get_next_id(MetadataPropertyAttributeType :: get_table_name());
	}

	function create_metadata_property_attribute_type($metadata_property_attribute_type)
	{
		return $this->create($metadata_property_attribute_type);
	}

	function update_metadata_property_attribute_type($metadata_property_attribute_type)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeType :: PROPERTY_ID, $metadata_property_attribute_type->get_id());
		return $this->update($metadata_property_attribute_type, $condition);
	}

	function delete_metadata_property_attribute_type($metadata_property_attribute_type)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeType :: PROPERTY_ID, $metadata_property_attribute_type->get_id());
		return $this->delete($metadata_property_attribute_type->get_table_name(), $condition);
	}

	function count_metadata_property_attribute_types($condition = null)
	{
		return $this->count_objects(MetadataPropertyAttributeType :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_attribute_type($id)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeType :: PROPERTY_ID, $id);
		return $this->retrieve_object(MetadataPropertyAttributeType :: get_table_name(), $condition,  null, MetadataPropertyAttributeType :: CLASS_NAME);
	}

	function retrieve_metadata_property_attribute_types($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(MetadataPropertyAttributeType :: get_table_name(), $condition, $offset, $max_objects, $order_by, MetadataPropertyAttributeType :: CLASS_NAME);
	}

	function get_next_metadata_property_attribute_value_id()
	{
		return $this->get_next_id(MetadataPropertyAttributeValue :: get_table_name());
	}

	function create_metadata_property_attribute_value($metadata_property_attribute_value)
	{
		return $this->create($metadata_property_attribute_value);
	}

	function update_metadata_property_attribute_value($metadata_property_attribute_value)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_ID, $metadata_property_attribute_value->get_id());
		return $this->update($metadata_property_attribute_value, $condition);
	}

	function delete_metadata_property_attribute_value($metadata_property_attribute_value)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_ID, $metadata_property_attribute_value->get_id());
		return $this->delete($metadata_property_attribute_value->get_table_name(), $condition);
	}

	function count_metadata_property_attribute_values($condition = null)
	{
		return $this->count_objects(MetadataPropertyAttributeValue :: get_table_name(), $condition);
	}

	function retrieve_metadata_property_attribute_value($id)
	{
		$condition = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_ID, $id);
		return $this->retrieve_object(MetadataPropertyAttributeValue :: get_table_name(), $condition, null,  MetadataPropertyAttributeValue :: CLASS_NAME);
	}

	function retrieve_metadata_property_attribute_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
            return $this->retrieve_objects(MetadataPropertyAttributeValue :: get_table_name(), $condition, $offset, $max_objects, $order_by, MetadataPropertyAttributeValue :: CLASS_NAME);
	}

        function retrieve_metadata_default_values($condition = null, $offset = null, $max_objects = null, $order_by = null)
        {
            return $this->retrieve_objects(MetadataDefaultValue :: get_table_name(), $condition, $offset, $max_objects, $order_by, MetadataDefaultValue :: CLASS_NAME);
        }

        function count_metadata_default_values($condition = null)
        {
            return $this->count_objects(MetadataDefaultValue :: get_table_name(), $condition);
        }

        function retrieve_metadata_default_value($id)
        {
            $condition = new EqualityCondition(MetadataDefaultValue :: PROPERTY_ID, $id);
            return $this->retrieve_object(MetadataDefaultValue :: get_table_name(), $condition,  null, MetadataDefaultValue :: CLASS_NAME);
        }

        function create_metadata_default_value(MetadataDefaultValue $metadata_default_value)
        {
            return $this->create($metadata_default_value);
        }

        function update_metadata_default_value(MetadataDefaultValue $metadata_default_value)
        {
            $condition = new EqualityCondition(MetadataDefaultValue :: PROPERTY_ID, $metadata_default_value->get_id());
            return $this->update($metadata_default_value, $condition);
        }

        function delete_metadata_default_value(MetadataDefaultValue $metadata_default_value)
        {
            $condition = new EqualityCondition(MetadataDefaultValue :: PROPERTY_ID, $metadata_default_value->get_id());
            return $this->delete(MetadataDefaultValue :: get_table_name(), $condition);
        }

        /*
         * retrieve prefixes of namespaces that have property types set
         */
        function retrieve_prefixes()
        {
            $namespace_alias = $this->get_alias(MetadataNamespace :: get_table_name());
            $type_alias = $this->get_alias(MetadataPropertyType :: get_table_name());

            $query = 'SELECT DISTINCT ' . $namespace_alias . '.' . MetadataNamespace :: PROPERTY_NS_PREFIX . ', ' . $namespace_alias . '.' . MetadataNamespace :: PROPERTY_ID;
            $query .= ' FROM ' . $this->escape_table_name(MetadataNamespace :: get_table_name()) . ' AS ' . $namespace_alias;
            $query .= ' INNER JOIN ' . $this->escape_table_name(MetadataPropertyType :: get_table_name()) . ' AS ' . $type_alias;
            $query .= ' ON ' . $this->escape_column_name(MetadataPropertyType :: PROPERTY_NAMESPACE, $type_alias) . ' = '. $this->escape_column_name(MetadataNamespace :: PROPERTY_ID, $namespace_alias);
            
            //$translator = new ConditionTranslator($this);
            //$query .= $translator->render_query(null);

            $res = $this->query($query);

            $prefixes = array();
            while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
                $prefixes[$record[MetadataNamespace :: PROPERTY_ID]] = $record[MetadataNamespace :: PROPERTY_NS_PREFIX];
            }
            $res->free();
            return $prefixes;
        }

        function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null){}
//
        function get_content_object_publication_attribute($publication_id){}

        function count_publication_attributes($user = null, $object_id = null, $condition = null){ return 0; }
}
?>