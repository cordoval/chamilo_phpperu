<?php
/**
 * @package metadata.tables.content_object_property_metadata_table
 */

require_once dirname(__FILE__).'/../../../tables/content_object_property_metadata_table/default_content_object_property_metadata_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../content_object_property_metadata.class.php';

/**
 * Table column model for the content_object_property_metadata browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class ContentObjectPropertyMetadataBrowserTableColumnModel extends DefaultContentObjectPropertyMetadataTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function ContentObjectPropertyMetadataBrowserTableColumnModel()
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