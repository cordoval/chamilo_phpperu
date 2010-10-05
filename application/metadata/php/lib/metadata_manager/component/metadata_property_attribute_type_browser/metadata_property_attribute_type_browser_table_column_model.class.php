<?php
/**
 * @package metadata.tables.metadata_property_attribute_type_table
 */

require_once dirname(__FILE__).'/../../../tables/metadata_property_attribute_type_table/default_metadata_property_attribute_type_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../metadata_property_attribute_type.class.php';

/**
 * Table column model for the metadata_property_attribute_type browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class MetadataPropertyAttributeTypeBrowserTableColumnModel extends DefaultMetadataPropertyAttributeTypeTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function MetadataPropertyAttributeTypeBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}

	/**
	 * Gets the modification column
	 * @return ContentObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new StaticTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>