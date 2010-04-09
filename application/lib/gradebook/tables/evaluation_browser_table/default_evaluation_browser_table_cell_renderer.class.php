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
		$optional_properties = $evaluation->get_optional_properties();
		switch ($column->get_name())
		{
			case Evaluation :: PROPERTY_EVALUATION_DATE :
				return $evaluation->get_evaluation_date();
			case Translation :: get('user'):
				return $optional_properties['user'];
			case Translation :: get('evaluator'):
				return $optional_properties['evaluator'];
			case GradeEvaluation :: PROPERTY_SCORE:
				return $optional_properties['score'];
			case GradeEvaluation :: PROPERTY_COMMENT:
				return $optional_properties['comment'];
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>