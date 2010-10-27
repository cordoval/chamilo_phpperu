<?php

namespace application\alexia;

use common\libraries\WebApplication;
use common\libraries\StaticTableColumn;
/**
 * $Id: alexia_publication_browser_table_column_model.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexiar.alexiar_manager.component.alexiapublicationbrowser
 */
require_once WebApplication :: get_application_class_lib_path('alexia') . 'tables/alexia_publication_table/default_alexia_publication_table_column_model.class.php';
/**
 * Table column model for the publication browser table
 */
class AlexiaPublicationBrowserTableColumnModel extends DefaultAlexiaPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function AlexiaPublicationBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return AlexiaTableColumn
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