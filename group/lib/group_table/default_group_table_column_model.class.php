<?php
/**
 * $Id: default_group_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_table
 */

/**
 * TODO: Add comment
 */
class DefaultGroupTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultGroupTableColumnModel()
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
        $columns[] = new ObjectTableColumn(Group :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(Group :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>