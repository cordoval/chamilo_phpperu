<?php
require_once dirname(__FILE__).'/../../evaluation.class.php';

class DefaultEvaluationBrowserTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultEvaluationBrowserTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Evaluation $evaluation
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $evaluation)
	{ 
		switch ($column->get_name())
		{
			case Evaluation :: PROPERTY_EVALUATION_DATE :
				return $format->get_evaluation_date();
			case Evaluation :: PROPERTY_EVLUATOR_ID :
				return $format->get_evaluator_id();
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>