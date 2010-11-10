<?php 
namespace application\metadata;
use common\libraries\ObjectTableCellRenderer;
use common\libraries\Translation;
use common\libraries\Utilities;
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
                                    return Translation :: get('Text', null, Utilities :: COMMON_LIBRARIES);
                                case ContentObjectPropertyMetadata :: SOURCE_CHAMILO_USER:
                                    return Translation :: get('chamiloUser', null, Utilities :: COMMON_LIBRARIES);
                                case ContentObjectPropertyMetadata :: SOURCE_TIMESTAMP:
                                    return Translation :: get('Timestamp', null, Utilities :: COMMON_LIBRARIES);
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