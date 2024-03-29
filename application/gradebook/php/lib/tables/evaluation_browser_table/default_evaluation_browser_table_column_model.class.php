<?php

namespace application\gradebook;

use common\libraries\WebApplication;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'grade_evaluation.class.php';

class DefaultEvaluationBrowserTableColumnModel extends ObjectTableColumnModel
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
		$gdm = GradebookDataManager :: get_instance();
		$evaluation_alias = $gdm->get_alias(Evaluation :: get_table_name());
		$grade_evaluation_alias = $gdm->get_alias(GradeEvaluation :: get_table_name());
		$columns = array();
		$columns[] = new ObjectTableColumn(Evaluation :: PROPERTY_EVALUATION_DATE, true, $evaluation_alias);
		$columns[] = new ObjectTableColumn('user', false);
		$columns[] = new ObjectTableColumn('evaluator', false);
		$columns[] = new ObjectTableColumn(GradeEvaluation :: PROPERTY_SCORE, true, $grade_evaluation_alias);
		$columns[] = new ObjectTableColumn(GradeEvaluation :: PROPERTY_COMMENT, false, $grade_evaluation_alias);
		return $columns;
	}
}
?>