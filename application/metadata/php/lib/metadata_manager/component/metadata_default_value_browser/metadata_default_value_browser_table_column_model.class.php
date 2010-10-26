<?php
namespace application\metadata;
use common\libraries\StaticTableColumn;
require_once dirname(__FILE__) . '/../../../tables/metadata_default_value_table/default_metadata_default_value_table_column_model.class.php';

/**
 * Table column model for the metadata_default_value browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class MetadataDefaultValueBrowserTableColumnModel extends DefaultMetadataDefaultValueTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function MetadataDefaultValueBrowserTableColumnModel()
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