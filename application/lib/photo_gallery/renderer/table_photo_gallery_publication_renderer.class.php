<?php
require_once dirname(__FILE__) . '/photo_gallery_publication_browser/photo_gallery_publication_browser_table.class.php';

class TablePhotoGalleryPublicationRenderer extends PhotoGalleryPublicationRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $table = new PhotoGalleryPublicationBrowserTable($this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>