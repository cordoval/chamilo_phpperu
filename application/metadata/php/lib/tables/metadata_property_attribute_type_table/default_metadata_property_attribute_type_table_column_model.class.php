<?php
/**
 * @package metadata.tables.metadata_property_attribute_type_table
 */
require_once dirname(__FILE__).'/../../metadata_property_attribute_type.class.php';

/**
 * Default column model for the metadata_property_attribute_type table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class DefaultMetadataPropertyAttributeTypeTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultMetadataPropertyAttributeTypeTableColumnModel()
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

		//$columns[] = new ObjectTableColumn(MetadataPropertyAttributeType :: PROPERTY_ID);
		$columns[] = new ObjectTableColumn(MetadataPropertyAttributeType :: PROPERTY_NS_PREFIX);
		$columns[] = new ObjectTableColumn(MetadataPropertyAttributeType :: PROPERTY_NAME);
		//$columns[] = new ObjectTableColumn(MetadataPropertyAttributeType :: PROPERTY_VALUE);
		//$columns[] = new ObjectTableColumn(MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE);

		return $columns;
	}
}
?>