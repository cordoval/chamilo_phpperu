<?php
/**
 * $Id: link_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.link_browser
 */
require_once dirname(__FILE__) . '/link_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../link_table/default_link_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LinkBrowserTableCellRenderer extends DefaultLinkTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LinkBrowserTableCellRenderer($browser, $type)
    {
        parent :: __construct($type);
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === LinkBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($content_object);
        }
        
        return parent :: render_cell($column, $content_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($content_object)
    {
        $toolbar_data = array();
        
//        $delete_url = $this->browser->get_content_object_delete_links_url($content_object);
//        $toolbar_data[] = array('href' => $delete_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
//        
//    	if (! $content_object->get_link_object()->is_latest_version())
//        {
//            $update_url = $this->browser->get_link_update_url($content_object);
//            $toolbar_data[] = array('href' => $update_url, 'label' => Translation :: get('Update'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_revert.png');
//        }
//        
//        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>