<?php
require_once dirname(__file__) . '/photobucket_external_repository_gallery_table_cell_renderer.class.php';
require_once dirname(__file__) . '/photobucket_external_repository_gallery_table_data_provider.class.php';
require_once dirname(__file__) . '/photobucket_external_repository_gallery_table_property_model.class.php';

class PhotobucketExternalRepositoryGalleryTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'photobucket_external_repository_gallery_table';

    function PhotobucketExternalRepositoryGalleryTable($browser, $parameters, $condition)
    {
        $data_provider = new PhotobucketExternalRepositoryGalleryTableDataProvider($browser, $condition);
        $renderer = new PhotobucketExternalRepositoryGalleryTableCellRenderer($browser);
        $property_model = new PhotobucketExternalRepositoryGalleryTablePropertyModel();

        parent :: __construct($data_provider, self :: DEFAULT_NAME, $renderer, $property_model);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(3);
        $this->set_default_column_count(3);
        //$this->set_order_directions_enabled($browser->support_sorting_direction());
    }
}
?>