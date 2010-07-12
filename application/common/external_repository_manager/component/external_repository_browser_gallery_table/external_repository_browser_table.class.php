<?php
require_once dirname (__FILE__) . '/external_repository_browser_table_cell_renderer.class.php';
require_once dirname (__FILE__) . '/external_repository_browser_table_data_provider.class.php';
require_once dirname (__FILE__) . '/external_repository_browser_table_property_model.class.php';

class ExternalRepositoryBrowserTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'external_repository_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ExternalRepositoryBrowserTable($browser, $parameters, $condition)
    {
        $renderer = new ExternalRepositoryBrowserTableCellRenderer($browser);
        $data_provider = new ExternalRepositoryBrowserTableDataProvider($browser, $condition);
        
        $property_model = $browser->get_property_model();
        if (!$property_model)
        {
            $property_model = new ExternalRepositoryBrowserPropertyModel();
        }
        
        parent :: __construct($data_provider, ExternalRepositoryBrowserTable :: DEFAULT_NAME, $renderer, $property_model);
        
        $this->set_additional_parameters($parameters);
//        $this->set_order_directions_enabled($browser->support_sorting_direction());
    }
}
?>