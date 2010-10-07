<?php
/**
 * $Id: gutenberg_publication_gallery_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'tables/gutenberg_publication_gallery_table/default_gutenberg_publication_gallery_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class GutenbergPublicationGalleryBrowserTableCellRenderer extends DefaultGutenbergPublicationGalleryTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function GutenbergPublicationGalleryBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($publication)
    {
        $html = array();
        $html[] = '<div style="width: 20px; float: right;">';
        $html[] = $this->get_modification_links($publication);
        $html[] = '</div>';
        $html[] = parent :: render_cell($publication);
        return implode("\n", $html);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    /**
     * @param ContentObject $publication
     */
    function get_cell_content(GutenbergPublication  $publication)
    {
        $content_object = $publication->get_publication_object();
        $display = ContentObjectDisplay :: factory($content_object);
        
        $html[] = '<h4>' . Utilities :: truncate_string($content_object->get_title(), 25) . '</h4>';
        $html[] = '<a href="' . htmlentities($this->browser->get_publication_viewing_url($publication)) . '">' . $display->get_preview(true) . '</a>';
        
        return implode("\n", $html);
    }

    private function get_modification_links($publication)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->browser->get_gutenberg_publication_actions($publication));
        return $toolbar->as_html();
    }
}
?>