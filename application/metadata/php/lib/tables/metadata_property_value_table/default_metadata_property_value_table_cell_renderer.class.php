<?php
/**
 * @package metadata.tables.metadata_property_value_table
 */

require_once dirname(__FILE__).'/../../metadata_property_value.class.php';

/**
 * Default cell renderer for the metadata_property_value table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultMetadataPropertyValueTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultMetadataPropertyValueTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param MetadataPropertyValue $metadata_property_value - The metadata_property_value
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $metadata_property_value)
	{
		switch ($column->get_name())
		{
			case MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID :
				return $metadata_property_value->get_content_object_id();
			case MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID :
				return $metadata_property_value->get_property_type_id();
			case MetadataPropertyValue :: PROPERTY_VALUE :
				return $metadata_property_value->get_value();
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