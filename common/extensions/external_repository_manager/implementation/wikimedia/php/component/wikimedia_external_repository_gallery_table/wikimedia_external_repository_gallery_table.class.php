<?php
namespace common\extensions\external_repository_manager\implementation\wikimedia;

use common\libraries\GalleryObjectTable;

require_once dirname(__file__) . '/wikimedia_external_repository_gallery_table_cell_renderer.class.php';
require_once dirname(__file__) . '/wikimedia_external_repository_gallery_table_data_provider.class.php';
require_once dirname(__file__) . '/wikimedia_external_repository_gallery_table_property_model.class.php';

class WikimediaExternalRepositoryGalleryTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'wikimedia_external_repository_gallery_table';

    function __construct($browser, $parameters, $condition)
    {
        $data_provider = new WikimediaExternalRepositoryGalleryTableDataProvider($browser, $condition);
        $renderer = new WikimediaExternalRepositoryGalleryTableCellRenderer($browser);
        $property_model = new WikimediaExternalRepositoryGalleryTablePropertyModel();

        parent :: __construct($data_provider, self :: DEFAULT_NAME, $renderer, $property_model);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(4);
        $this->set_default_column_count(4);
        //        $this->set_order_directions_enabled($browser->support_sorting_direction());
    }
}
?>