<?php

namespace application\cda;

use common\libraries\WebApplication;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;
use common\libraries\Utilities;
/**
 * @package cda.tables.language_pack_table
 */
require_once WebApplication :: get_application_class_lib_path('cda') . 'tables/language_pack_table/default_language_pack_table_column_model.class.php';

/**
 * Table column model for the language_pack browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class LanguagePackBrowserTableColumnModel extends DefaultLanguagePackTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function LanguagePackBrowserTableColumnModel($browser)
	{
		parent :: __construct();
		$this->set_default_order_column(1);

		if(Utilities :: get_classname_from_namespace(get_class($browser)) != 'CdaManagerAdminLanguagePacksBrowserComponent')
		{
		    $this->add_column(new StaticTableColumn(Translation :: get('Status')));
			$this->add_column(new StaticTableColumn(Translation :: get('TranslationProgress')));
		}

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