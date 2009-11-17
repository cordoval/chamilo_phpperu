<?php
/**
 * $Id: user_view_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.user_view_browser
 */


/**
 * Table column model for the repository browser table
 */
class UserViewBrowserTableColumnModel extends ObjectTableColumnModel
{

    function UserViewBrowserTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
        $this->set_default_order_column(1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(UserView :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(UserView :: PROPERTY_DESCRIPTION);
        $columns[] = self :: get_modification_column();
        return $columns;
    }
    
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>
