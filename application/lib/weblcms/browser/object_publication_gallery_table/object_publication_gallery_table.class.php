<?php
require_once dirname(__FILE__) . '/object_publication_gallery_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/object_publication_gallery_table_data_provider.class.php';

class ObjectPublicationGalleryTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'object_publication_gallery_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ObjectPublicationGalleryTable($table_renderer, $owner, $types, $condition, $cell_renderer = null)
    {
        $data_provider = new ObjectPublicationGalleryTableDataProvider($table_renderer, $owner, $types, $condition);

        if (! $cell_renderer)
        {
            $cell_renderer = new ObjectPublicationGalleryTableCellRenderer($table_renderer);
        }

        parent :: __construct($data_provider, self :: DEFAULT_NAME, $cell_renderer/*, $table_renderer->get_sort_properties()*/);
        $this->set_additional_parameters($parameters);
//        $this->set_order_directions_enabled($browser->support_sorting_direction());
    }
}
?>