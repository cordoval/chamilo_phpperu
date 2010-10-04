<?php
/**
 * $Id: default_location_table_column_model.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.tables.location_table
 */

/**
 * TODO: Add comment
 */
class DefaultLocationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultLocationTableColumnModel()
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
        $columns[] = new ObjectTableColumn(Location :: PROPERTY_LOCATION);
        //		$columns[] = new ObjectTableColumn(Location :: PROPERTY_TYPE);
        //		$columns[] = new ObjectTableColumn(Location :: PROPERTY_LOCKED);
        //		$columns[] = new ObjectTableColumn(Location :: PROPERTY_INHERIT);
        return $columns;
    }
}
?>