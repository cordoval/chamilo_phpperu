<?php
require_once dirname(__FILE__) . '/object_publication_gallery_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/object_publication_gallery_table_data_provider.class.php';
require_once dirname(__FILE__) . '/object_publication_gallery_table_property_model.class.php';

class ObjectPublicationGalleryTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'object_publication_gallery_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ObjectPublicationGalleryTable($table_renderer, $condition, $cell_renderer = null, $property_model = null)
    {
        $data_provider = new ObjectPublicationGalleryTableDataProvider($table_renderer, $condition);
        
        if (! $property_model)
        {
            $property_model = new ObjectPublicationGalleryTablePropertyModel();
        }
        
        if (! $cell_renderer)
        {
            $cell_renderer = new ObjectPublicationGalleryTableCellRenderer($table_renderer);
        }
        
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $cell_renderer, $property_model);
        $this->set_additional_parameters($table_renderer->get_tool_browser()->get_parameters());
//        $this->set_order_directions_enabled($table_renderer->get_tool_browser()->is_gallery_table_sorting_direction_enabled());
    }
}
?>