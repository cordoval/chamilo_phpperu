<?php
/**
 * @package metadata.tables.metadata_property_type_table
 */
require_once dirname(__FILE__).'/../../metadata_property_type.class.php';

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
	function DefaultMetadataPropertyTypeTableColumnModel()
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
		$columns[] = new ObjectTableColumn(MetadataPropertyType :: PROPERTY_NS_PREFIX);
		$columns[] = new ObjectTableColumn(MetadataPropertyType :: PROPERTY_NAME);

		return $columns;
	}
}
?>