<?php
namespace menu;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * $Id: default_navigation_item_table_column_model.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.navigation_item_table
 */

class DefaultNavigationItemTableColumnModel extends ObjectTableColumnModel
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
     * @return MenuManagerTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn('Type', false);
        $columns[] = new ObjectTableColumn(NavigationItem :: PROPERTY_TITLE, false);
        return $columns;
    }
}
?>