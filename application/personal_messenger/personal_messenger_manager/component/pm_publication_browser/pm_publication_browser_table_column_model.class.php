<?php
/**
 * $Id: pm_publication_browser_table_column_model.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component.pm_publication_browser
 */
require_once dirname(__FILE__) . '/../../../pm_publication_table/default_pm_publication_table_column_model.class.php';
/**
 * Table column model for the publication browser table
 */
class PmPublicationBrowserTableColumnModel extends DefaultPmPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function PmPublicationBrowserTableColumnModel($folder)
    {
        parent :: __construct($folder);
        $this->set_default_order_column(3);
        $this->set_default_order_direction(SORT_DESC);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return PersonalMessagePublicationTableColumn
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