<?php
require_once dirname(__FILE__) . '/photo_gallery_gallery_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/photo_gallery_gallery_browser_table_property_model.class.php';
require_once dirname(__FILE__) . '/photo_gallery_gallery_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class PhotoGalleryGalleryBrowserTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'photo_gallery_gallery_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function PhotoGalleryGalleryBrowserTable($browser, $parameters, $condition)
    {
        $property_model = new PhotoGalleryGalleryBrowserTablePropertyModel();
        $cell_renderer = new PhotoGalleryGalleryBrowserTableCellRenderer($browser);
        $data_provider = new PhotoGalleryGalleryBrowserTableDataProvider($browser, $condition);

        parent :: __construct($data_provider, self :: DEFAULT_NAME, $cell_renderer, $property_model);

        $this->set_default_row_count(4);
        $this->set_default_column_count(4);
        $this->set_additional_parameters($parameters);
    }
}
?>