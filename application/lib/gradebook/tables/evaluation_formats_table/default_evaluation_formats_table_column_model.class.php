<?php
require_once dirname(__FILE__).'/../../format.class.php';

class DefaultEvaluationFormatsTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultEvaluationFormatsTableColumnModel()
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
		$columns[] = new ObjectTableColumn(Format :: PROPERTY_ACTIVE);
		return $columns;
	}
}
?>