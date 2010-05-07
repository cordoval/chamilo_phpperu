<?php
/**
 * $Id: default_period_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package period.lib.period_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerPeriodTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerPeriodTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(InternshipOrganizerPeriod :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION);
        $columns[] = new ObjectTableColumn(InternshipOrganizerPeriod :: PROPERTY_BEGIN);
        $columns[] = new ObjectTableColumn(InternshipOrganizerPeriod :: PROPERTY_END);
        return $columns;
    }
}
?>