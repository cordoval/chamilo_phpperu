<?php
require_once dirname (__FILE__) . '/../../table/default_streaming_media_object_table_cell_renderer.class.php';

class StreamingMediaBrowserTableCellRenderer extends DefaultStreamingMediaObjectTableCellRenderer
{
/**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function StreamingMediaBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }
}
?>