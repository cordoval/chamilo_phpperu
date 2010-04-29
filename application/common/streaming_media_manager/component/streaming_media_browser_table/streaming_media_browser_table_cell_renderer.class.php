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
    
 	function render_cell($object)
    {
        $html = array();
        
        $html[] = '<h3>' . $object->get_title() . ' (' . Utilities :: format_seconds_to_minutes($object->get_duration()) .')</h3>';
        $html[] = '<a href="' . $this->browser->get_streaming_media_object_viewing_url($object) . '"> <img src="' . $object->get_thumbnail() . '"/></a> <br/>';
        $html[] = '<i>' . Utilities ::truncate_string($object->get_description(), 100) . '</i><br/>';
        
        return implode("\n", $html);
    }
}
?>