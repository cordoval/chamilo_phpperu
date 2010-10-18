<?php
require_once WebApplication :: get_application_class_lib_path('photo_gallery') . 'tables/photo_gallery_gallery_table/default_photo_gallery_gallery_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class PhotoGalleryPublicationGalleryBrowserTableCellRenderer extends DefaultPhotoGalleryGalleryTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function PhotoGalleryPublicationGalleryBrowserTableCellRenderer($browser)
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
    function get_cell_content(PhotoGalleryPublication  $photo_gallery)
    {
        $content_object = $photo_gallery->get_publication_object();
        $display = ContentObjectDisplay :: factory($content_object);
        
        $html[] = '<h4>' . Utilities :: truncate_string($content_object->get_title(), 25) . '</h4>';
        $html[] = '<a href="' . htmlentities($this->browser->get_publication_viewing_url($photo_gallery)) . '">' . $display->get_preview(true) . '</a>';
        
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