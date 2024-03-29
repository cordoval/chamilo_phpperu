<?php

namespace application\gradebook;

use common\libraries\WebApplication;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

require_once WebApplication :: get_application_class_lib_path('gradebook') . 'format.class.php';

class DefaultEvaluationFormatsTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent :: __construct(self :: get_default_columns());
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(Format :: PROPERTY_TITLE);
		return $columns;
	}
}
?>