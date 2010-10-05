<?php
/**
 * $Id: profile_publication_browser_table_column_model.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component.profile_publication_browser
 */
require_once dirname(__FILE__) . '/../../../profile_publication_table/default_profile_publication_table_column_model.class.php';
/**
 * Table column model for the publication browser table
 */
class ProfilePublicationBrowserTableColumnModel extends DefaultProfilePublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function ProfilePublicationBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ProfileTableColumn
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