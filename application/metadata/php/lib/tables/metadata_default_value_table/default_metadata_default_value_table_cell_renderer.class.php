<?php
namespace application\metadata;
use common\libraries\ObjectTableCellRenderer;
/**
 * Default cell renderer for the metadata_default_value table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultMetadataDefaultValueTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultMetadataDefaultValueTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param MetadataDefaultValue $metadata_default_value - The metadata_default_value
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $metadata_default_value)
	{
		switch ($column->get_name())
		{
			case MetadataDefaultValue :: PROPERTY_PROPERTY_TYPE_ID :
				return $metadata_default_value->get_property_type_id();
			case MetadataDefaultValue :: PROPERTY_VALUE :
				return $metadata_default_value->get_value();
			case MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID :
				return $metadata_default_value->get_property_attribute_type_id();
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