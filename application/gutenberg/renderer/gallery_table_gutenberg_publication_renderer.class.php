<?php
/**
 * $Id: table_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/../gutenberg_publication_renderer.class.php';
require_once dirname(__FILE__) . '/gutenberg_publication_gallery_browser/gutenberg_publication_gallery_browser_table.class.php';
/**
 * Renderer to display a sortable table with learning object publications.
 */
class GalleryTableGutenbergPublicationRenderer extends GutenbergPublicationRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $table = new GutenbergPublicationGalleryBrowserTable($this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>