<?php
/**
 * $Id: webservice_browser_table_column_model.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib.webservice_manager.component.webservice_browser_table
 */
require_once dirname(__FILE__) . '/../../../webservice_table/default_webservice_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class WebserviceBrowserTableColumnModel extends DefaultWebserviceTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function WebserviceBrowserTableColumnModel()
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