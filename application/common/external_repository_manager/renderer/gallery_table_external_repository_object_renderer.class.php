<?php
/**
 * $Id: table_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/../external_repository_object_renderer.class.php';
require_once dirname(__FILE__) . '/../component/external_repository_browser_gallery_table/external_repository_browser_gallery_table.class.php';
/**
 * Renderer to display a sortable table with learning object publications.
 */
class GalleryTableExternalRepositoryObjectRenderer extends ExternalRepositoryObjectRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $external_repository_manager_type = $this->get_external_repository_browser()->get_parameter(ExternalRepositoryManager :: PARAM_TYPE);
        $table = ExternalRepositoryBrowserGalleryTable :: factory($external_repository_manager_type, $this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>