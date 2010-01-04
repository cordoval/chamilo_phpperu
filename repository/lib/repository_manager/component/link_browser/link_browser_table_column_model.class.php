<?php
/**
 * $Id: link_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.link_browser
 */
require_once dirname(__FILE__) . '/../../../link_table/default_link_table_column_model.class.php';
/**
 * Table column model for the link browser table
 */
class LinkBrowserTableColumnModel extends DefaultLinkTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function LinkBrowserTableColumnModel($type)
    {
        parent :: __construct($type);
        $this->set_default_order_column(0);
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
