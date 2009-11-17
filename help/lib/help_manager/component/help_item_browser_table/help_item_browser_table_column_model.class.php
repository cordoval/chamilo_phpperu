<?php
/**
 * $Id: help_item_browser_table_column_model.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.help_manager.component.help_item_browser_table
 */
require_once dirname(__FILE__) . '/../../../help_item_table/default_help_item_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class HelpItemBrowserTableColumnModel extends DefaultHelpItemTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function HelpItemBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

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
