<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\StaticTableColumn;
/**
 * @package package.tables.package_language_table
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'tables/package_table/default_package_table_column_model.class.php';

/**
 * Table column model for the package_language browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class PackageBrowserTableColumnModel extends DefaultPackageTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function __construct()
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