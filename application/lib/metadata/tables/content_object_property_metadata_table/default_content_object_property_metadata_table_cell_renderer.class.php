<?php
/**
 * @package metadata.tables.content_object_property_metadata_table
 */

require_once dirname(__FILE__).'/../../content_object_property_metadata.class.php';

/**
 * Default cell renderer for the content_object_property_metadata table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultContentObjectPropertyMetadataTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultContentObjectPropertyMetadataTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param ContentObjectPropertyMetadata $content_object_property_metadata - The content_object_property_metadata
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $content_object_property_metadata)
	{
		switch ($column->get_name())
		{
			case DefaultContentObjectPropertyMetadataTableColumnModel :: COLUMN_TYPE_PROPERTY_TYPE :
				return $content_object_property_metadata->get_property_type_id();
			case ContentObjectPropertyMetadata :: PROPERTY_CONTENT_OBJECT_PROPERTY :
				return $content_object_property_metadata->get_content_object_property();
                        case ContentObjectPropertyMetadata :: PROPERTY_SOURCE :
                            switch ($content_object_property_metadata->get_source())
                            {
                                case ContentObjectPropertyMetadata :: SOURCE_TEXT:
                                    return Translation :: get('text');
                                case ContentObjectPropertyMetadata :: SOURCE_CHAMILO_USER:
                                    return Translation :: get('chamiloUser');
                                case ContentObjectPropertyMetadata :: SOURCE_TIMESTAMP:
                                    return Translation :: get('timestamp');
                            }
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