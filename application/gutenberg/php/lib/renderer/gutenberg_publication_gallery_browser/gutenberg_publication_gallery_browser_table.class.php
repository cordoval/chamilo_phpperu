<?php
/**
 * $Id: repository_browser_gallery_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'renderer/gutenberg_publication_gallery_browser/gutenberg_publication_gallery_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'renderer/gutenberg_publication_gallery_browser/gutenberg_publication_gallery_browser_table_property_model.class.php';
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'renderer/gutenberg_publication_gallery_browser/gutenberg_publication_gallery_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class GutenbergPublicationGalleryBrowserTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'gutenberg_publication_gallery_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function GutenbergPublicationGalleryBrowserTable($browser, $parameters, $condition)
    {
        $property_model = new GutenbergPublicationGalleryBrowserTablePropertyModel();
        $cell_renderer = new GutenbergPublicationGalleryBrowserTableCellRenderer($browser);
        $data_provider = new GutenbergPublicationGalleryBrowserTableDataProvider($browser, $condition);

        parent :: __construct($data_provider, self :: DEFAULT_NAME, $cell_renderer, $property_model);

        $this->set_default_row_count(4);
        $this->set_default_column_count(4);
        $this->set_additional_parameters($parameters);
    }
}
?>