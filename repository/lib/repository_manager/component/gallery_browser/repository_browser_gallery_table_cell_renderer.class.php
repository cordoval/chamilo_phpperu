<?php
/**
 * $Id: repository_browser_gallery_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/../../../content_object_table/default_content_object_gallery_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RepositoryBrowserGalleryTableCellRenderer extends DefaultContentObjectGalleryTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function RepositoryBrowserGalleryTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($content_object)
    {
        $html = array();
        $html[] = '<div style="width: 20px; float: right;">';
        $html[] = $this->get_modification_links($content_object);
        $html[] = '</div>';
        $html[] = parent :: render_cell($content_object);
        return implode("\n", $html);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    /**
     * @param ContentObject $content_object
     */
    function get_cell_content(ContentObject $content_object)
    {
        $display = ContentObjectDisplay :: factory($content_object);
        
        $html[] = '<h4>' . Utilities :: truncate_string($content_object->get_title(), 25) . '</h4>';
        $html[] = '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($content_object)) . '">' . $display->get_preview(true) . '</a>';
        
        return implode("\n", $html);
    }

    private function get_modification_links($content_object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->browser->get_content_object_actions($content_object));
        return $toolbar->as_html();
    }
}
?>