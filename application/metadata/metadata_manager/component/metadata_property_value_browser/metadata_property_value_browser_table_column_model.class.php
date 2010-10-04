<?php
/**
 * @package metadata.tables.metadata_property_value_table
 */

require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../metadata_property_value.class.php';

/**
 * Table column model for the metadata_property_value browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class MetadataPropertyValueBrowserTableColumnModel extends DefaultContentObjectTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function MetadataPropertyValueBrowserTableColumnModel()
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