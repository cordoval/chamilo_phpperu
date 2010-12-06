<?php 
namespace application\metadata;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

/**
 * Default column model for the metadata_property_type table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultMetadataPropertyTypeTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
         const COLUMN_PREFIX = 'ns_prefix';

	function __construct()
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

		//$columns[] = new ObjectTableColumn(MetadataPropertyType :: PROPERTY_ID);
		$columns[] = new ObjectTableColumn(self :: COLUMN_PREFIX);
		$columns[] = new ObjectTableColumn(MetadataPropertyType :: PROPERTY_NAME);

		return $columns;
	}
}
?>