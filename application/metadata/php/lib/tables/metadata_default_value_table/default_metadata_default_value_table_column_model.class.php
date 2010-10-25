<?php
namespace application\metadata;
use common\libraries\ObjectTableColumnModel;
/**
 * Default column model for the metadata_default_value table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultMetadataDefaultValueTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultMetadataDefaultValueTableColumnModel()
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

		//$columns[] = new ObjectTableColumn(MetadataDefaultValue :: PROPERTY_ID);
		$columns[] = new ObjectTableColumn(MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID);
		$columns[] = new ObjectTableColumn(MetadataDefaultValue :: PROPERTY_VALUE);

		return $columns;
	}
}
?>