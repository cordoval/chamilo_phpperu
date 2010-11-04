<?php

namespace application\metadata;

use common\libraries\StaticTableColumn;
use common\libraries\ObjectTableColumn;
use group\DefaultGroupTableColumnModel;
use common\libraries\Path;

require_once Path :: get_group_path() . 'lib/group_table/default_group_table_column_model.class.php';

/**
 * Table column model for the metadata_property_value browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class GroupMetadataPropertyValueBrowserTableColumnModel extends DefaultGroupTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function GroupMetadataPropertyValueBrowserTableColumnModel()
	{
            $this->add_column(new ObjectTableColumn(GroupMetadataPropertyValue :: PROPERTY_GROUP_ID));
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