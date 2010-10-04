<?php
/**
 * $Id: default_category_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.category_table
 */
require_once dirname(__FILE__) . '/../../item.class.php';

/**
 * TODO: Add comment
 */
class DefaultCategoryTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultCategoryTableColumnModel()
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
        $columns[] = new StaticTableColumn('', false);
        $columns[] = new ObjectTableColumn(Category :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(Category :: PROPERTY_POOL, true);
        return $columns;
    }
}
?>