<?php
require_once dirname(__file__) . '/external_repository_browser_gallery_table_cell_renderer.class.php';
require_once dirname(__file__) . '/external_repository_browser_gallery_table_data_provider.class.php';
require_once dirname(__file__) . '/external_repository_browser_gallery_table_property_model.class.php';

class ExternalRepositoryBrowserGalleryTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'external_repository_browser_gallery_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ExternalRepositoryBrowserGalleryTable($browser, $parameters, $condition)
    {
        $data_provider = new ExternalRepositoryBrowserGalleryTableDataProvider($browser, $condition);

        $renderer = $browser->get_external_repository_browser_gallery_table_cell_renderer();
        if (! $renderer)
        {
            $renderer = new ExternalRepositoryBrowserGalleryTableCellRenderer($browser);
        }

        $property_model = $browser->get_external_repository_browser_gallery_table_property_model();
        if (! $property_model)
        {
            $property_model = new ExternalRepositoryBrowserGalleryPropertyModel();
        }

        parent :: __construct($data_provider, ExternalRepositoryBrowserGalleryTable :: DEFAULT_NAME, $renderer, $property_model);

        $this->set_additional_parameters($parameters);
        //        $this->set_order_directions_enabled($browser->support_sorting_direction());
    }
}
?>