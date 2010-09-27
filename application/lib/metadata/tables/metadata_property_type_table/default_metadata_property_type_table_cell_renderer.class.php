<?php
/**
 * @package metadata.tables.metadata_property_type_table
 */

require_once dirname(__FILE__).'/../../metadata_property_type.class.php';

/**
 * Default cell renderer for the metadata_property_type table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultMetadataPropertyTypeTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultMetadataPropertyTypeTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param MetadataPropertyType $metadata_property_type - The metadata_property_type
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $metadata_property_type)
	{
		switch ($column->get_name())
		{
			case MetadataPropertyType :: PROPERTY_ID :
				return $metadata_property_type->get_id();
			case MetadataPropertyType :: PROPERTY_NS_PREFIX :
				return $metadata_property_type->get_ns_prefix();
			case MetadataPropertyType :: PROPERTY_NAME :
				return $metadata_property_type->get_name();
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