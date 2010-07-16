<?php
/**
 * $Id: external_repository_instance_browser_table_column_model.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/../../external_repository_instance_table/default_external_repository_instance_table_column_model.class.php';
/**
 * Table column model for the repository browser table
 */
class ExternalRepositoryInstanceBrowserTableColumnModel extends DefaultExternalRepositoryInstanceTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function ExternalRepositoryInstanceBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
//        $this->add_column(new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE));
//        $this->add_column(new StaticTableColumn(Translation :: get('Versions')));
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