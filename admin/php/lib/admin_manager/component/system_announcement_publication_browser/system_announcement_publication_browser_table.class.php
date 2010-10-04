<?php
/**
 * $Id: system_announcement_publication_browser_table.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component.system_announcement_publication_table
 */
require_once dirname(__FILE__) . '/system_announcement_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/system_announcement_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/system_announcement_publication_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class SystemAnnouncementPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'sys_browser_table';

    /**
     * Constructor
     */
    function SystemAnnouncementPublicationBrowserTable($browser, $name, $parameters, $condition)
    {
        $model = new SystemAnnouncementPublicationBrowserTableColumnModel();
        $renderer = new SystemAnnouncementPublicationBrowserTableCellRenderer($browser);
        $data_provider = new SystemAnnouncementPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SystemAnnouncementPublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(AdminManager :: PARAM_DELETE_SELECTED, Translation :: get('RemoveSelected'));
        
        if ($browser->get_user()->is_platform_admin())
        {
            $this->set_form_actions($actions);
        }
        $this->set_default_row_count(20);
    }
}
?>