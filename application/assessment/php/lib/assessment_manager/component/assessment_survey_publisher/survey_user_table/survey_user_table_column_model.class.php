<?php

namespace application\assessment;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;
use common\libraries\Utilities;
/**
 * $Id: survey_user_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component.assessment_survey_publisher.survey_user_table
 */
/**
 * This class represents a column model for a publication candidate table
 */
class SurveyUserTableColumnModel extends ObjectTableColumnModel
{
    /**
     * The column with the action buttons.
     */
    private static $action_column;

    /**
     * Constructor.
     */
    function SurveyUserTableColumnModel()
    {
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
        $columns[] = new ObjectTableColumn(SurveyInvitation :: PROPERTY_USER_ID);
        $columns[] = new ObjectTableColumn(SurveyInvitation :: PROPERTY_EMAIL);
        $columns[] = new ObjectTableColumn(SurveyInvitation :: PROPERTY_VALID);
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
            self :: $action_column = new StaticTableColumn(Translation :: get('Actions', null, Utilities :: COMMON_LIBRARIES));
        }
        return self :: $action_column;
    }
}
?>