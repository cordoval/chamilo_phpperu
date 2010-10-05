<?php
/**
 * $Id: quota_browser_table_cell_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.quota_browser
 */
require_once dirname(__FILE__) . '/quota_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/quota_table/default_quota_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../quota.class.php';
require_once dirname(__FILE__) . '/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class QuotaBrowserTableCellRenderer extends DefaultQuotaTableCellRenderer
{
    /**
     * The repository browser component
     */
    protected $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function QuotaBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    function render_cell($column, $quota)
    {
        if ($column === QuotaBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($quota);
        }
        
        return parent :: render_cell($column, $quota);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($quota)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_update_quota_url($quota->get_id()),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_delete_quota_url($quota->get_id()),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));
        
        return $toolbar->as_html();
    }
}
?>