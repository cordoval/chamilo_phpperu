<?php
/**
 * @package metadata.tables.metadata_property_attribute_type_table
 */

require_once dirname(__FILE__).'/../../metadata_property_attribute_type.class.php';

/**
 * Default cell renderer for the metadata_property_attribute_type table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultMetadataPropertyAttributeTypeTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultMetadataPropertyAttributeTypeTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param MetadataPropertyAttributeType $metadata_property_attribute_type - The metadata_property_attribute_type
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $metadata_property_attribute_type)
	{
		switch ($column->get_name())
		{
			case MetadataPropertyAttributeType :: PROPERTY_ID :
				return $metadata_property_attribute_type->get_id();
			case MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX :
				return $metadata_property_attribute_type->get_ns_prefix();
			case MetadataPropertyAttributeType :: PROPERTY_NAME :
				return $metadata_property_attribute_type->get_name();
			case MetadataPropertyAttributeType :: PROPERTY_VALUE :
				return $metadata_property_attribute_type->get_value();
			case MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE :
				return $metadata_property_attribute_type->get_value_type();
			default :
				return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>