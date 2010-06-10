<?php
/**
 * $Id: phrases_publication_browser_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component.phrases_publication_browser
 */

require_once dirname(__FILE__) . '/../../../../../tables/phrases_publication_table/default_phrases_publication_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../../../phrases_publication.class.php';

/**
 * Table column model for the phrases_publication browser table
 *
 * @author Hans De Bisschop
 * @author 
 */

class PhrasesPublicationBrowserTableColumnModel extends DefaultPhrasesPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function PhrasesPublicationBrowserTableColumnModel()
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