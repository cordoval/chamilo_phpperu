<?php
require_once dirname(__FILE__) . '/../../tables/photo_gallery_gallery_table/default_photo_gallery_gallery_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class PhotoGalleryGalleryBrowserTableCellRenderer extends DefaultPhotoGalleryGalleryTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function PhotoGalleryGalleryBrowserTableCellRenderer($browser)
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
    function get_cell_content(PhotoGallery  $photo_gallery)
    {
        $content_object = $photo_gallery->get_photo_gallery_object();
        $display = ContentObjectDisplay :: factory($content_object);
        
        $html[] = '<h4>' . Utilities :: truncate_string($content_object->get_title(), 25) . '</h4>';
        //$html[] = '<a href="' . htmlentities($this->browser->get_publication_viewing_url($photo_gallery)) . '">' . $display->get_preview(true) . '</a>';
        
        return implode("\n", $html);
    }

    private function get_modification_links($publication)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->browser->get_photo_gallery_actions($publication));
        return $toolbar->as_html();
    }
}
?>