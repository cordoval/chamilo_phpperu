<?php
/**
 * $Id: gutenberg_publication_browser_table_column_model.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenbergr.gutenbergr_manager.component.gutenbergpublicationbrowser
 */
require_once dirname(__FILE__) . '/../../tables/gutenberg_publication_table/default_gutenberg_publication_table_column_model.class.php';
/**
 * Table column model for the publication browser table
 */
class GutenbergPublicationBrowserTableColumnModel extends DefaultGutenbergPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function GutenbergPublicationBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return GutenbergTableColumn
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