<?php
/**
 * $Id: default_registration_table_column_model.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.tables.registration_table
 */

/**
 * TODO: Add comment
 */
class DefaultRegistrationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultRegistrationTableColumnModel()
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
        $columns[] = new ObjectTableColumn(Registration :: PROPERTY_TYPE);
        $columns[] = new ObjectTableColumn(Registration :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(Registration :: PROPERTY_VERSION);
        //$columns[] = new ObjectTableColumn(Registration :: PROPERTY_STATUS);
        return $columns;
    }
}
?>