<?php

namespace application\reservations;

use common\libraries\ObjectTableColumnModel;
use common\libraries\StaticTableColumn;
use common\libraries\ObjectTableColumn;
/**
 * $Id: default_item_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.item_table
 */
/**
 * TODO: Add comment
 */
class DefaultItemTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
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
        $columns[] = new StaticTableColumn('');
        $columns[] = new ObjectTableColumn(Item :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(Item :: PROPERTY_DESCRIPTION, true);
        $columns[] = new ObjectTableColumn(Item :: PROPERTY_RESPONSIBLE, true);
        $columns[] = new ObjectTableColumn(Item :: PROPERTY_CREDITS, true);
        return $columns;
    }
}
?>