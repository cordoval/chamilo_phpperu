<?php
/**
 * $Id: default_event_table_column_model.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.event_table
 */

/**
 * TODO: Add comment
 */
class DefaultEventTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultEventTableColumnModel()
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
        $columns[] = new ObjectTableColumn(Event :: PROPERTY_BLOCK, true);
        $columns[] = new ObjectTableColumn(Event :: PROPERTY_NAME, true);
        return $columns;
    }
}
?>