<?php
/**
 * $Id: system_announcement_publication_browser_table_column_model.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.admin_manager.component.system_announcement_publication_table
 */
require_once dirname(__FILE__) . '/../../../system_announcement_publication_table/default_system_announcement_publication_table_column_model.class.php';
/**
 * Table column model for the publication browser table
 */
class SystemAnnouncementPublicationBrowserTableColumnModel extends DefaultSystemAnnouncementPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SystemAnnouncementPublicationBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->set_default_order_direction(SORT_DESC);
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
