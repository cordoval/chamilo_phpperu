<?php
require_once dirname(__file__) . '/picasa_external_repository_gallery_table_cell_renderer.class.php';
require_once dirname(__file__) . '/picasa_external_repository_gallery_table_data_provider.class.php';
require_once dirname(__file__) . '/picasa_external_repository_gallery_table_property_model.class.php';

class PicasaExternalRepositoryGalleryTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'picasa_external_repository_gallery_table';

    function PicasaExternalRepositoryGalleryTable($browser, $parameters, $condition)
    {
        $data_provider = new PicasaExternalRepositoryGalleryTableDataProvider($browser, $condition);
        $renderer = new PicasaExternalRepositoryGalleryTableCellRenderer($browser);
        $property_model = new PicasaExternalRepositoryGalleryTablePropertyModel();

        parent :: __construct($data_provider, self :: DEFAULT_NAME, $renderer, $property_model);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(4);
        $this->set_default_column_count(4);
        //        $this->set_order_directions_enabled($browser->support_sorting_direction());
    }
}
?>