<?php 
namespace application\metadata;
use common\libraries\ObjectTableColumnModel;

/**
 * Default column model for the metadata_property_value table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultMetadataPropertyValueTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultMetadataPropertyValueTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
		$columns = array();

		//$columns[] = new ObjectTableColumn(MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID);
		$columns[] = new ObjectTableColumn(MetadataPropertyValue :: PROPERTY_PROPERTY_TYPE_ID);
		$columns[] = new ObjectTableColumn(MetadataPropertyValue :: PROPERTY_VALUE);

		return $columns;
	}
}
?>