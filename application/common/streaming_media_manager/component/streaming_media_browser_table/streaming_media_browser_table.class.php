<?php
require_once dirname (__FILE__) . '/streaming_media_browser_table_cell_renderer.class.php';
require_once dirname (__FILE__) . '/streaming_media_browser_table_data_provider.class.php';

class StreamingMediaBrowserTable extends GalleryObjectTable
{
    const DEFAULT_NAME = 'streaming_media_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function StreamingMediaBrowserTable($browser, $parameters, $condition)
    {
        $renderer = new StreamingMediaBrowserTableCellRenderer($browser);
        $data_provider = new StreamingMediaBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, StreamingMediaBrowserTable :: DEFAULT_NAME, $renderer, $browser->get_sort_properties());
        $this->set_additional_parameters($parameters);
        $this->set_order_directions_enabled($browser->support_sorting_direction());
    }
}
?>