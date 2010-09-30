<?php
/**
 * $Id: table_content_object_publication_list_renderer.class.php
 */
require_once dirname(__FILE__) . '/../photo_gallery_publication_renderer.class.php';
require_once dirname(__FILE__) . '/photo_gallery_publication_gallery_browser/photo_gallery_publication_gallery_browser_table.class.php';

class GalleryTablePhotoGalleryPublicationRenderer extends PhotoGalleryPublicationRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $table = new PhotoGalleryPublicationGalleryBrowserTable($this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>