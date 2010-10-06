<?php
require_once WebApplication :: get_application_class_lib_path('photo_gallery') . 'renderer/photo_gallery_publication_gallery_browser/photo_gallery_publication_gallery_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('photo_gallery') . 'renderer/photo_gallery_publication_gallery_browser/photo_gallery_publication_gallery_browser_table_property_model.class.php';
require_once WebApplication :: get_application_class_lib_path('photo_gallery') . 'renderer/photo_gallery_publication_gallery_browser/photo_gallery_publication_gallery_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class PhotoGalleryPublicationGalleryBrowserTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'photo_gallery_publication_gallery_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function PhotoGalleryPublicationGalleryBrowserTable($browser, $parameters, $condition)
    {
        $property_model = new PhotoGalleryPublicationGalleryBrowserTablePropertyModel();
        $cell_renderer = new PhotoGalleryPublicationGalleryBrowserTableCellRenderer($browser);
        $data_provider = new PhotoGalleryPublicationGalleryBrowserTableDataProvider($browser, $condition);

        parent :: __construct($data_provider, self :: DEFAULT_NAME, $cell_renderer, $property_model);

        $this->set_default_row_count(4);
        $this->set_default_column_count(4);
        $this->set_additional_parameters($parameters);
    }
}
?>