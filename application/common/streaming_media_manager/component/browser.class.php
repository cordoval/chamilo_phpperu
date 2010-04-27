<?php
require_once dirname(__FILE__) . '/streaming_media_browser_table/streaming_media_browser_table.class.php';
class StreamingMediaBrowserComponent extends StreamingMediaComponent
{
    private $menu;

    function get_menu()
    {
        return $this->menu;
    }

    function set_menu($menu)
    {
        $this->menu = $menu;
    }

    function render_menu()
    {
        $menu = new StreamingMediaMenu(Request :: get(StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID), $this->get_parent());
        $html = array();
        $html[] = '<div style=" width: 20%; overflow: auto; float: left">';
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function run()
    {
        $streaming_media_objects = $this->retrieve_streaming_media_objects();
        $this->display_header();
        
    	if ($this->get_menu() == null)
        {
        	echo($this->render_menu());
        	
        }
        
        echo('<div style=" width: 80%; overflow: auto; float: center">');

        $browser_table = new StreamingMediaBrowserTable($this, $this->get_parameters(), null);
        echo($browser_table->as_html());
        echo('</div>');
        
        $this->display_footer();
    }
}
?>