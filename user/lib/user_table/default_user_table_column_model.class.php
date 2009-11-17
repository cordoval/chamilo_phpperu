<?php
/**
 * $Id: default_user_table_column_model.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_table
 */

class DefaultUserTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultUserTableColumnModel()
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
        //$columns[] = new ObjectTableColumn(User :: PROPERTY_PICTURE_URI);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_OFFICIAL_CODE);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME);
        return $columns;
    }
}
?>