<?php

namespace application\metadata;

use common\libraries\StaticTableColumn;
use common\libraries\ObjectTableColumn;
use user\DefaultUserTableColumnModel;
use common\libraries\Path;

require_once Path :: get_user_path() . 'lib/user_table/default_user_table_column_model.class.php';

/**
 * Table column model for the metadata_property_value browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class UserMetadataPropertyValueBrowserTableColumnModel extends DefaultUserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function UserMetadataPropertyValueBrowserTableColumnModel()
	{
            $this->add_column(new ObjectTableColumn(UserMetadataPropertyValue :: PROPERTY_USER_ID));
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