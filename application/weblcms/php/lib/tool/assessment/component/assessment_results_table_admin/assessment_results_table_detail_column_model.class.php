<?php
/**
 * $Id: assessment_results_table_detail_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_results_table_admin
 */
/**
 * This class represents a column model for a publication candidate table
 */
class AssessmentResultsTableDetailColumnModel extends ObjectTableColumnModel
{
    /**
     * The column with the action buttons.
     */
    private static $action_column;
    private static $user_assessment;

    /**
     * Constructor.
     */
    function AssessmentResultsTableDetailColumnModel($user_assessment)
    {
        self :: $user_assessment = $user_assessment;
        parent :: __construct(self :: get_columns(), 1, SORT_ASC);
    }

    /**
     * Gets the columns of this table.
     * @return array An array of all columns in this table.
     * @see ContentObjectTableColumn
     */
    function get_columns()
    {
        $columns = array();
        $columns[] = new StaticTableColumn(Translation :: get(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID));
        $columns[] = new StaticTableColumn(Translation :: get(WeblcmsAssessmentAttemptsTracker :: PROPERTY_DATE));
        $pub = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication(self :: $user_assessment);
        $assessment = $pub->get_content_object();
        if ($assessment->get_type() != Survey :: get_type_name())
            $columns[] = new StaticTableColumn(Translation :: get(WeblcmsAssessmentAttemptsTracker :: PROPERTY_TOTAL_SCORE));
        $columns[] = self :: get_action_column();
        return $columns;
    }

    /**
     * Gets the column wich contains the action buttons.
     * @return ContentObjectTableColumn The action column.
     */
    static function get_action_column()
    {
        if (! isset(self :: $action_column))
        {
            self :: $action_column = new StaticTableColumn(Translation :: get('Actions'));
        }
        return self :: $action_column;
    }
}
?>