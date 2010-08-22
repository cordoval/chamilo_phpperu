<?php
/**
 * $Id: gutenberg_publication_browser_table_cell_renderer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenbergr.gutenbergr_manager.component.gutenbergpublicationbrowser
 */
require_once dirname(__FILE__) . '/gutenberg_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../tables/gutenberg_publication_table/default_gutenberg_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../gutenberg_manager/gutenberg_manager.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class GutenbergPublicationBrowserTableCellRenderer extends DefaultGutenbergPublicationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param GutenbergManagerBrowserComponent $browser
     */
    function GutenbergPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $gutenberg_publication)
    {
        if ($column === GutenbergPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($gutenberg_publication);
        }
        
        switch ($column->get_name())
        {
//            case ContentObject :: PROPERTY_TITLE :
//                $title = parent :: render_cell($column, $gutenberg_publication);
//                $title_short = Utilities :: truncate_string($title, 53, false);
//                return '<a href="' . htmlentities($gutenberg_publication->get_publication_object()->get_url()) . '" title="' . $title . '">' . $title_short . '</a>';
        }
        
        return parent :: render_cell($column, $gutenberg_publication);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $gutenberg The gutenberg object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($gutenberg_publication)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->browser->get_gutenberg_publication_actions($gutenberg_publication));
        return $toolbar->as_html();
        
    	$toolbar = new Toolbar(); 
        
        $viewing_url = $this->browser->get_publication_viewing_url($gutenberg_publication);
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_details.png', $viewing_url, ToolbarItem :: DISPLAY_ICON));
        
        if ($this->browser->get_user()->is_platform_admin() || $gutenberg_publication->get_publisher() == $this->browser->get_user()->get_id())
        {
            $edit_url = $this->browser->get_publication_editing_url($gutenberg_publication);
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $edit_url, ToolbarItem :: DISPLAY_ICON));
            
            $delete_url = $this->browser->get_publication_deleting_url($gutenberg_publication);
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $delete_url, ToolbarItem :: DISPLAY_ICON, true));
        }
        
        return $toolbar->as_html();
    }
}
?>