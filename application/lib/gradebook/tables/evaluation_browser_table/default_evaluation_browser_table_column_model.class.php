<?php
require_once dirname(__FILE__).'/../../evaluation.class.php';

class DefaultEvaluationBrowserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultEvaluationBrowserTableColumnModel()
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
		$columns[] = new ObjectTableColumn(Evaluation :: PROPERTY_EVALUATION_DATE);
		$columns[] = new ObjectTableColumn(Evaluation :: PROPERTY_EVALUATOR_ID);
		return $columns;
	}
}
?>