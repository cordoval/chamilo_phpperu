<?php
/**
 * $Id: default_user_table_column_model.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_table
 */

class DefaultDynamicFormElementTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultDynamicFormElementTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return UserTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(DynamicFormElement :: PROPERTY_TYPE);
        $columns[] = new ObjectTableColumn(DynamicFormElement :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(DynamicFormElement :: PROPERTY_REQUIRED);
        return $columns;
    }
}
?>